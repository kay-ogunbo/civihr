<?php

use CRM_HRLeaveAndAbsences_BAO_LeaveRequest as LeaveRequest;

/**
 * Class leaveDateHoursAmountDeduction
 */
class CRM_HRLeaveAndAbsences_Service_LeaveDateHoursAmountDeduction
  implements CRM_HRLeaveAndAbsences_Service_LeaveDateAmountDeduction {

  /**
   * Calculate the amount to be deducted in hours for a Leave Request
   * date using the work day information the leave date falls on and
   * also the leave date type.
   *
   * @param \DateTime $leaveDateTime
   * @param array $workDay
   * @param CRM_HRLeaveAndAbsences_BAO_LeaveRequest $leaveRequest
   *
   * @return float
   */
  public function calculate(DateTime $leaveDateTime, $workDay, LeaveRequest $leaveRequest) {
    return empty($workDay['number_of_hours']) ? 0 : (float)$workDay['number_of_hours'];
  }
}
