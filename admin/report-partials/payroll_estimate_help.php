<div class="modal fade" id="modal_payroll_estimate_help" tabindex="-1" role="dialog" aria-labelledby="payroll_estimate_help_title" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="payroll_estimate_help_title">Payroll Estimate Help</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <h4><u>How it works</u></h4>
        <p>
            Payroll estimate is different from the payroll report in that it <i>assumes an employee's hours</i> instead of calculating actual worked hours.
            The days an employee has already worked will be calculated as actual hours, but all days that they have not worked and do not have reason for being absent,
            will be estimated.
        </p>
        <p>
            Here's a breakdown of how estimated hours are calculated:
            <ul>
                <li>If an employee has a Clock-in and Clock-out for that day, their actual hours will be calculated.</li>
                <li>Otherwise their hours will be calculated based on their expected Clock-in and Clock-out times (these can be changed in user management).</li>
                <li>If an employee is arriving early, late, or leaving early, those will be factored into the estimated hours.</li>
                <ul>
                    <li>NOTE: If an employee has a Clock-in and Clock-out time, the estimated hours will prioritize those over the above arrive/leave times.</li>
                </ul>
                <li>Unpaid hours and PTO are also included in the estimated hours.</li>
                <li>If an employee is absent for a whole day without PTO, they will have zero hours for that day.</li>
            </ul>
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
