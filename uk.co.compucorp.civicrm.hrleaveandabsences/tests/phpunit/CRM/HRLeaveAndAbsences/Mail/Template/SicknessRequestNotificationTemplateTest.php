<?php

use CRM_HRLeaveAndAbsences_Mail_Template_SicknessRequestNotificationTemplate as SicknessRequestNotificationTemplate;
use CRM_HRLeaveAndAbsences_Service_LeaveRequestComment as LeaveRequestCommentService;
use CRM_HRLeaveAndAbsences_BAO_LeaveRequest as LeaveRequest;
use CRM_HRLeaveAndAbsences_Test_Fabricator_LeaveRequest as LeaveRequestFabricator;


/**
 * Class RM_HRLeaveAndAbsences_Mail_SicknessRequestNotificationTemplateTest
 *
 * @group headless
 */
class CRM_HRLeaveAndAbsences_Mail_Template_SicknessRequestNotificationTemplateTest extends BaseHeadlessTest {

  use CRM_HRLeaveAndAbsences_LeaveRequestHelpersTrait;
  use CRM_HRLeaveAndAbsences_LeaveManagerHelpersTrait;
  use CRM_HRLeaveAndAbsences_MailHelpersTrait;


  private $sicknessRequestNotificationTemplate;

  public function setUp() {
    CRM_Core_DAO::executeQuery('SET foreign_key_checks = 0;');
    $leaveRequestCommentService = new LeaveRequestCommentService();
    $this->sicknessRequestNotificationTemplate = new SicknessRequestNotificationTemplate($leaveRequestCommentService);

    $this->leaveRequestStatuses = $this->getLeaveRequestStatuses();
    $this->leaveRequestDayTypes = $this->getLeaveRequestDayTypes();
  }

  public function testGetTemplateIDReturnsTheCorrectID() {
    $templateDetails = $this->getTemplateDetails(['msg_title' => 'CiviHR Sickness Record Notification']);
    $templateID = $this->sicknessRequestNotificationTemplate->getTemplateID();
    $this->assertEquals($templateID, $templateDetails['id']);
  }

  public function testGetTemplateParametersReturnsTheExpectedParametersForTheTemplate() {
    $leaveRequest = LeaveRequestFabricator::fabricateWithoutValidation([
      'type_id' => 1,
      'contact_id' =>2,
      'from_date' => CRM_Utils_Date::processDate('tomorrow'),
      'to_date' => CRM_Utils_Date::processDate('tomorrow'),
      'sickness_reason' => 1,
      'sickness_required_documents' => 1,
      'request_type' => LeaveRequest::REQUEST_TYPE_SICKNESS
    ], false);

    //create 2 attachments for Sickness Request
    $attachment1 = $this->createAttachmentForLeaveRequest([
      'entity_id' => $leaveRequest->id,
      'name' => 'LeaveRequestSampleFile1.txt'
    ]);

    $attachment2 = $this->createAttachmentForLeaveRequest([
      'entity_id' => $leaveRequest->id,
      'name' => 'LeaveRequestSampleFile2.txt'
    ]);

    //add one comment for the Sickness request
    $params = [
      'leave_request_id' => $leaveRequest->id,
      'text' => 'Random Commenter',
      'contact_id' => $leaveRequest->contact_id,
      'sequential' => 1
    ];

    $leaveRequestCommentService = new LeaveRequestCommentService();
    $leaveRequestCommentService->add($params);
    $leaveRequestCommentService->add(array_merge($params, ['text' => 'Sample text']));

    $tplParams = $this->sicknessRequestNotificationTemplate->getTemplateParameters($leaveRequest);

    $leaveRequestDayTypes = LeaveRequest::buildOptions('from_date_type');
    $leaveRequestStatuses = LeaveRequest::buildOptions('status_id');
    $sicknessReasons = LeaveRequest::buildOptions('sickness_reason');
    $fromDate = new DateTime($leaveRequest->from_date);
    $toDate = new DateTime($leaveRequest->to_date);

    //validate template parameters
    $this->assertEquals($tplParams['toDate'], $toDate->format('Y-m-d'));
    $this->assertEquals($tplParams['fromDate'], $fromDate->format('Y-m-d'));
    $this->assertEquals($tplParams['leaveRequest'], $leaveRequest);
    $this->assertEquals($tplParams['fromDateType'], $leaveRequestDayTypes[$leaveRequest->from_date_type]);
    $this->assertEquals($tplParams['toDateType'], $leaveRequestDayTypes[$leaveRequest->to_date_type]);
    $this->assertEquals($tplParams['leaveStatus'], $leaveRequestStatuses[$leaveRequest->status_id]);
    $this->assertEquals($tplParams['leaveRequestLink'], CRM_Utils_System::url('my-leave', [], true));

    //There are two attachments for the Sickness request
    $this->assertCount(2, $tplParams['leaveFiles']);
    foreach($tplParams['leaveFiles'] as $file) {
      $this->assertContains($file['name'], [
        'LeaveRequestSampleFile1.txt', 'LeaveRequestSampleFile2.txt'
      ]);
    }

    //there are two comment for the Sickness request
    $this->assertCount(2, $tplParams['leaveComments']);
    foreach($tplParams['leaveComments'] as $comment) {
      $this->assertContains($comment['text'], ['Random Commenter', 'Sample text']);
      $this->assertEquals($comment['leave_request_id'], $leaveRequest->id);
    }

    $this->assertEquals($tplParams['sicknessReasons'], $sicknessReasons);
    $this->assertEquals($tplParams['sicknessRequiredDocuments'], $this->getSicknessRequiredDocuments());
    $this->assertEquals($tplParams['leaveRequiredDocuments'], explode(',', $leaveRequest->sickness_required_documents));
  }
}
