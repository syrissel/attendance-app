<div class="modal fade" id="modal_payroll_help" tabindex="-1" role="dialog" aria-labelledby="payroll_help_title" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="payroll_help_title">Payroll Help</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <h4><u>How it works</u></h4>
        <p>
            Payroll is determined by <i>actual hours</i>, in other words, the actual amount of hours an employee spends in the building.
            This report also takes into account hours that an employee may have used for time off and time used for unpaid breaks like lunch. Here's a general breakdown
            for calculating an employee's hours for a single day:
            <ul>
                <li><b>Worked Hours</b> = total actual hours spent working before PTO.</li>
                <li><b>PTO</b> = paid time off.</li>
                <li><b>Unpaid Hours</b> = the hours an employee has not worked and will not be paid for during the pay period.</li>
                <li><b>Payable Hours</b> = the net amount of hours an employee will be paid for (Worked Hours + PTO)</li>
                <li>
                    <b>Unpaid Breaks</b> - two unpaid breaks may be subtracted from an employee's worked hours: <b>lunch</b> and <b>unpaid break</b>.
                    A 30 minute lunch will be subtracted for each shift worked at 5 hours or more. The unpaid break is a break where an
                    employee voluntarily and temporarily clocks out for an allotted amount of time and will not be paid for the time they are away.
                </li>
                <li>
                    Hours are calculated by the user's Clock-in and Clock-out times.
                    <ul>
                        <li>If an employee does not have either a Clock-in or a Clock-out for that day, they will have zero hours for that day.</li>
                        <li>All employees that have a Clock-in but not a Clock-out will show up on a notice above the payroll report. It's up to a manager to either adjust their records (recommended if it's a past payroll period), or to finalize it (for current payroll periods).</li>
                    </ul>
                </li>
                <li>If an employee has PTO hours, it will be added to their total payable hours.</li>
                <li>An employee's unpaid breaks will be subtracted from their worked hours.</li>
            </ul>
        </p>
        <h4><u>What is the <i>Finalized Payroll</i>?</u></h4>
        <p>
            The Finalized Payroll means that the system will calculate the remainder of the pay period even if employees do not have Clock-in records for those dates.
            The system will assume an employee will be working from their expected Clock-in and Clock-out times each day <b><i>unless:</i></b>
            <ul>
                <li>The employee has a sick day.</li>
                <li>The employee is arriving late, arriving early, or leaving early.</li>
                <li>NOTE: Once the employee clocks in and out for an arriving late, arriving early, or leaving early day, the system will use the Clock-in and Clock-out records instead of the late/early times to determine hours worked.</li>
            </ul>
        </p>
        <h4><u>Editing Records from This Report</u></h4>
        <p>
            It's possible to edit an employee's records from the current payroll period. On the payroll report, simply click on an employee's name
            and a new tab will open with all of the employee's attendance records for the current payroll period. Create new records or edit existing ones
            as you please!
        </p>
        <h4><u>Hour Rounding</u></h4>
        <p>
            The total amount of hours are rounded to the nearest quarter-hour based on the following:
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Rounded Quarter</th>
                        <th>Minutes</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>0:00</td>
                        <td>0:01 - 0:08</td>
                    </tr>
                    <tr>
                        <td>0:15 or .25</td>
                        <td>0:09 - 0:23</td>
                    </tr>
                    <tr>
                        <td>0:30 or .5</td>
                        <td>0:24 - 0:38</td>
                    </tr>
                    <tr>
                        <td>0:45 or .75</td>
                        <td>0:39 - 0:53</td>
                    </tr>
                    <tr>
                        <td>1:00 (round to next hour)</td>
                        <td>0:54 - 0:59</td>
                    </tr>
                </tbody>
            </table>
        </p>
        <h4><u>Comments</u></h4>
        <p>
            Add comments to the payroll report by clicking on the comments column in an employee's row. This will show on the report when printed, but
            will disappear once the page is refreshed.
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
