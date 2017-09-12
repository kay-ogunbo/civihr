<?php

use CRM_HRLeaveAndAbsences_Test_Fabricator_AbsenceType as AbsenceTypeFabricator;
use CRM_HRLeaveAndAbsences_Test_Fabricator_AbsencePeriod as AbsencePeriodFabricator;
use CRM_Hrjobcontract_Test_Fabricator_HRJobContract as HRJobContractFabricator;
use CRM_HRLeaveAndAbsences_Test_Fabricator_LeavePeriodEntitlement as LeavePeriodEntitlementFabricator;
use CRM_HRLeaveAndAbsences_Test_Fabricator_WorkPattern as WorkPatternFabricator;
use CRM_HRLeaveAndAbsences_BAO_LeaveRequest as LeaveRequest;
use CRM_HRLeaveAndAbsences_BAO_LeaveBalanceChange as LeaveBalanceChange;
use CRM_HRLeaveAndAbsences_Service_LeaveRequestComment as LeaveRequestCommentService;

/**
 * Class CRM_HRLeaveAndAbsences_Import_Parser_BaseTest
 *
 * @group headless
 */
class CRM_HRLeaveAndAbsences_Import_Parser_BaseTest extends BaseHeadlessTest {

  use CRM_HRLeaveAndAbsences_LeaveBalanceChangeHelpersTrait;

  private $absenceType;

  public function setUp() {
    $session = CRM_Core_Session::singleton();
    $session->set('dateTypes', 1);
    $this->absenceType = AbsenceTypeFabricator::fabricate(['title' => 'Holiday']);
  }

  private function getImportObject($fields)  {
    $importObject = new CRM_HRLeaveAndAbsences_Import_Parser_Base($fields);
    $importObject->init();

    return $importObject;
  }

  public function testLeaveRequestWithBalanceChangeAndCommentsCanBeCreatedFromImportParameters() {
    $period = AbsencePeriodFabricator::fabricate([
      'start_date' => CRM_Utils_Date::processDate('2016-01-01'),
      'end_date'   => CRM_Utils_Date::processDate('2016-12-31'),
    ]);

    $periodEntitlement = LeavePeriodEntitlementFabricator::fabricate([
      'type_id' => $this->absenceType->id,
      'contact_id' => 1,
      'period_id' => $period->id
    ]);

    $this->createLeaveBalanceChange($periodEntitlement->id, 3);

    HRJobContractFabricator::fabricate(
      ['contact_id' => $periodEntitlement->contact_id],
      ['period_start_date' => '2016-01-01']
    );

    WorkPatternFabricator::fabricateWithA40HourWorkWeek(['is_default' => 1]);

    $contactId = 1;

    $row1 = [
      'contact_id' => $contactId,
      'absence_id' => 1,
      'absence_type' => $this->absenceType->title,
      'absence_date' => '2016-01-01',
      'qty' => 2,
      'start_date' => '2016-01-01',
      'end_date' => '2016-01-02',
      'total_qty' => 3,
      'status' => 'Approved',
      'comments' => 'Test Comment',
    ];

    $fields = array_keys($row1);
    $importObject = $this->getImportObject($fields);

    $row2 = $row1;
    $row2['qty'] = 1;
    $row2['absence_date'] = '2016-01-02';

    $valuesRow1 = array_values($row1);
    $valuesRow2 = array_values($row2);
    $response1 = $importObject->import(NULL, $valuesRow1);
    $response2 = $importObject->import(NULL, $valuesRow2);

    $this->assertEquals(CRM_Import_Parser::VALID, $response1);
    $this->assertEquals(CRM_Import_Parser::VALID, $response2);

    $leaveRequest = new LeaveRequest();
    $leaveRequest->from_date = CRM_Utils_Date::processDate('2016-01-01');
    $leaveRequest->to_date = CRM_Utils_Date::processDate('2016-01-02');
    $leaveRequest->contact_id = $contactId;
    $leaveRequest->find(true);

    $this->assertNotNull($leaveRequest->id);
    //a total balance change of -3 is expected, i.e the value in total_qty field
    $balanceChange = LeaveBalanceChange::getTotalBalanceChangeForLeaveRequest($leaveRequest);
    $this->assertEquals(-3, $balanceChange);

    //Check that comment was properly created
    $leaveRequestCommentService = new LeaveRequestCommentService();
    $result = $leaveRequestCommentService->get(['leave_request_id' => $leaveRequest->id, 'sequential' => 1]);
    $this->assertEquals($result['values'][0]['text'], $row1['comments']);
    $this->assertEquals($result['values'][0]['contact_id'], $row1['contact_id']);
    $commentDate = new DateTime('2016-01-01');
    $expectedCommentDate = new DateTime($result['values'][0]['created_at']);
    $this->assertEquals($expectedCommentDate, $commentDate);
  }

  /**
   * @expectedException CRM_HRLeaveAndAbsences_Exception_InvalidLeaveRequestException
   * @expectedExceptionMessage Contact does not have period entitlement for the absence type
   */
  public function testValidateLeaveParamsThrowsExceptionWhenContactHasNoPeriodEntitlementForTheAbsenceType() {
    AbsencePeriodFabricator::fabricate([
      'start_date' => CRM_Utils_Date::processDate('2016-01-01'),
      'end_date'   => CRM_Utils_Date::processDate('2016-12-31'),
    ]);

    $contactId = 1;
    $row1 = [
      'contact_id' => $contactId,
      'absence_id' => 1,
      'type_id' => $this->absenceType->id,
      'absence_date' => '2016-01-01',
      'qty' => 2,
      'start_date' => '2016-01-01',
      'end_date' => '2016-01-01',
      'total_qty' => 3,
      'status_id' => 1,
      'request_type' => LeaveRequest::REQUEST_TYPE_LEAVE,
    ];

    $fields = array_keys($row1);
    $importObject = $this->getImportObject($fields);
    $importObject->validateLeaveParams($row1);
  }

  public function testValidateLeaveParamsThrowsExceptionWhenBalanceIsGreaterThanEntitlementBalanceAndAllowOveruseFalse() {
    $period = AbsencePeriodFabricator::fabricate([
      'start_date' => CRM_Utils_Date::processDate('2016-01-01'),
      'end_date'   => CRM_Utils_Date::processDate('2016-12-31'),
    ]);

    $absenceType = AbsenceTypeFabricator::fabricate([
      'title' => 'Type 1',
      'allow_overuse' => 0
    ]);

    $periodEntitlement = LeavePeriodEntitlementFabricator::fabricate([
      'type_id' => $absenceType->id,
      'contact_id' => 1,
      'period_id' => $period->id
    ]);

    $entitlementBalanceChange = 3;
    $this->createLeaveBalanceChange($periodEntitlement->id, $entitlementBalanceChange);

    HRJobContractFabricator::fabricate(
      ['contact_id' => $periodEntitlement->contact_id],
      ['period_start_date' => '2016-01-01']
    );

    WorkPatternFabricator::fabricateWithA40HourWorkWeek(['is_default' => 1]);

    //This will create a balance change of 4 since the value for total_qty
    //is 4
    $row1 = [
      'contact_id' => $periodEntitlement->contact_id,
      'absence_id' => 1,
      'type_id' => $absenceType->id,
      'absence_date' => '2016-01-01',
      'qty' => 4,
      'start_date' => '2016-01-01',
      'end_date' => '2016-01-01',
      'total_qty' => 4,
      'status_id' => 1,
      'request_type' => LeaveRequest::REQUEST_TYPE_LEAVE,
    ];

    $this->setExpectedException(
      'CRM_HRLeaveAndAbsences_Exception_InvalidLeaveRequestException',
      'There are only '. $entitlementBalanceChange. ' days leave available. This request cannot be made or approved'
    );

    $fields = array_keys($row1);
    $importObject = $this->getImportObject($fields);
    $importObject->validateLeaveParams($row1);
  }

  public function testValidateLeaveParamsDoesNotThrowsExceptionWhenBalanceIsGreaterThanEntitlementBalanceWhenAllowOveruseFalseAndRequestTypeIsToil() {
    $period = AbsencePeriodFabricator::fabricate([
      'start_date' => CRM_Utils_Date::processDate('2016-01-01'),
      'end_date'   => CRM_Utils_Date::processDate('2016-12-31'),
    ]);

    $absenceType = AbsenceTypeFabricator::fabricate([
      'title' => 'Type 1',
      'allow_overuse' => 0
    ]);

    $periodEntitlement = LeavePeriodEntitlementFabricator::fabricate([
      'type_id' => $absenceType->id,
      'contact_id' => 1,
      'period_id' => $period->id
    ]);

    $entitlementBalanceChange = 3;
    $this->createLeaveBalanceChange($periodEntitlement->id, $entitlementBalanceChange);

    HRJobContractFabricator::fabricate(
      ['contact_id' => $periodEntitlement->contact_id],
      ['period_start_date' => '2016-01-01']
    );

    WorkPatternFabricator::fabricateWithA40HourWorkWeek(['is_default' => 1]);

    //This will create a balance change of 4 since the value for total_qty
    //is 4
    $row1 = [
      'contact_id' => $periodEntitlement->contact_id,
      'absence_id' => 1,
      'type_id' => $absenceType->id,
      'absence_date' => '2016-01-01',
      'qty' => 4,
      'start_date' => '2016-01-01',
      'end_date' => '2016-01-01',
      'total_qty' => 4,
      'status_id' => 1,
      'request_type' => LeaveRequest::REQUEST_TYPE_TOIL,
    ];

    $fields = array_keys($row1);
    $importObject = $this->getImportObject($fields);
    //Since the request type is TOIL, balance change validation will not
    //be taken into account.
    $importObject->validateLeaveParams($row1);
  }

  public function testValidateLeaveParamsDoesNotThrowsExceptionWhenBalanceIsGreaterThanEntitlementBalanceWhenAllowOveruseFalseAndRequestIsCancelled() {
    $period = AbsencePeriodFabricator::fabricate([
      'start_date' => CRM_Utils_Date::processDate('2016-01-01'),
      'end_date'   => CRM_Utils_Date::processDate('2016-12-31'),
    ]);

    $absenceType = AbsenceTypeFabricator::fabricate([
      'title' => 'Type 1',
      'allow_overuse' => 0
    ]);

    $periodEntitlement = LeavePeriodEntitlementFabricator::fabricate([
      'type_id' => $absenceType->id,
      'contact_id' => 1,
      'period_id' => $period->id
    ]);

    $entitlementBalanceChange = 3;
    $this->createLeaveBalanceChange($periodEntitlement->id, $entitlementBalanceChange);

    HRJobContractFabricator::fabricate(
      ['contact_id' => $periodEntitlement->contact_id],
      ['period_start_date' => '2016-01-01']
    );

    WorkPatternFabricator::fabricateWithA40HourWorkWeek(['is_default' => 1]);
    LeaveRequest::getCancelledStatuses();
    //This will create a balance change of 4 since the value for total_qty
    //is 4
    $row1 = [
      'contact_id' => $periodEntitlement->contact_id,
      'absence_id' => 1,
      'type_id' => $absenceType->id,
      'absence_date' => '2016-01-01',
      'qty' => 4,
      'start_date' => '2016-01-01',
      'end_date' => '2016-01-01',
      'total_qty' => 4,
      'request_type' => LeaveRequest::REQUEST_TYPE_LEAVE,
    ];

    $fields = array_keys($row1);
    $importObject = $this->getImportObject($fields);
    //Since the request type is TOIL, balance change validation will not
    //be taken into account.
    foreach(LeaveRequest::getCancelledStatuses() as $status) {
      $row1['status_id'] = $status;
      $importObject->validateLeaveParams($row1);
    }
  }
}
