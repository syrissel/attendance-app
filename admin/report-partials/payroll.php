<?php if (count($ar_err) > 0): ?>

<div class="alert alert-warning w-100 alert-dismissible fade show" role="alert">
<strong>Warning!</strong> Some users have missing clock-outs for this pay period. 
        Their hours will not be accounted for those days. You can check their records
        by clicking on their name in this list or in the table below. Adjust their hours 
        accordingly through the <a href="users.php">user management portal</a>. Or <a href="#btn_finalize_payroll">generate
        a payroll estimate</a>.
<hr />
<div>
<span><u>Users:</u></span>
<ul class="list-unstyled pt-2">
    <?php foreach ($ar_err as $err): ?>
        <li class="pl-2"><a target="_blank" href="employee_attendance_report.php?id=<?= $err->getID() ?>&date_range=<?= $start_date . '_' . $end_date ?>" style="color:#856404;"><?= $err->getFullName() ?></a></li>
    <?php endforeach ?>
</ul>
</div>
<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
</button>
</div>

<?php endif ?>
<table class="table table-borderless table-striped table-hover">
    <thead>
        <tr>
            <th class="text-center" colspan="8">
                <div class="d-flex justify-content-center">
                <u><h4 class="py-3 font-italic align-self-center">
                    Payroll 
                    <?= date('F d, Y', strtotime($start_date)) . ' - ' . date('F d, Y', strtotime($end_date))  ?>
                </u></h4>
                <div class="align-self-end ml-auto my-3">
                    <button type="button" class="btn btn-link" id="print_button" onclick="window.print();return false;">Print</button>
                    <button type="button" class="btn btn-link" id="help_button">Help</button>
                </div>
                </div>
            </th>
        </tr>
        <tr>
        <th colspan="2" scope="col"><u>Name</u></th>
        <th scope="col"><u>Position</u></th>
        <th scope="col" class="text-center"><u>Worked Hours</u></th>
        <th scope="col" class="text-center"><u>PTO</u></th>
        <th scope="col" class="text-center"><u>Unpaid</u></th>
        <th scope="col" class="text-center"><u>Payable Hours</u></th>
        <th scope="col" class="text-center"><u>Comments</u></th>
        </tr>
    </thead>
    <tbody>
            <?php foreach ($ar as $a): ?>
                <tr>
                    <td colspan="2"><b><a data-toggle="tooltip" data-placement="right" title="View biweekly report" class="text-body bs-tooltip" target="_blank" href="employee_attendance_report.php?id=<?= $a->getUser()->getID() ?>&date_range=<?= $start_date . '_' . $end_date ?>"><?= $a->getUser()->getFullName() ?></a></b></td>
                    <td><?= $a->getUser()->getUserPosition()->getPosition() ?></td>
                    <td class="text-center"><?= getTotalPaidHours($a->getTotalMinutes()) ?></td>
                    <td class="text-center"><?= minutesToHours($a->getTotalPTO()) ?></td>
                    <td class="text-center"><?= $a->getNonPaidHoursDifference() ?></td>
                    <td class="text-center font-weight-bold"><?= minutesToHours($a->getTotalNetMinutes()) ?></td>
                    <td><input type="text" name="comments" class="comments w-100" style="border:none;"></td>
                </tr>
            <?php endforeach ?>
        <tr>
            <td colspan="2"><b><?= count(User::getAdmins()) ?> Full-time</b></td>
            <th class="text-right">
                Employee Hours:
            </th>
            <td class="text-center bg-light"><b><?= getTotalPaidHours(AttendanceRange::addTotalMinutes(AttendanceRange::getFullTimeRecords($ar, $period))) ?></b></td>
            <td class="text-center bg-light"><b><?= minutesToHours(AttendanceRange::addTotalPTO(AttendanceRange::getFullTimeRecords($ar, $period))) ?></b></td>
            <td class="text-center bg-light"><b><?= AttendanceRange::addTotalNonPaidHours(AttendanceRange::getFullTimeRecords($ar, $period)) ?></b></td>
            <td class="text-center bg-light"><b><?= minutesToHours(AttendanceRange::addTotalPTOAndMinutes(AttendanceRange::getFullTimeRecords($ar, $period))) ?></b></td>
        </tr>
        <tr>
            <td colspan="2"><b><?= count(User::getAllInterns()) ?> Intern<?= count(User::getAllInterns()) > 1 ? 's' : '' ?></b></td>
            <th class="text-right">
                Intern Hours:
            </th>
            <td class="text-center bg-light"><b><?= getTotalPaidHours(AttendanceRange::addTotalMinutes(AttendanceRange::getInternRecords($ar, $period))) ?></b></td>
            <td class="text-center bg-light"><b><?= minutesToHours(AttendanceRange::addTotalPTO(AttendanceRange::getInternRecords($ar, $period))) ?></b></td>
            <td class="text-center bg-light"><b><?= AttendanceRange::addTotalNonPaidHours(AttendanceRange::getInternRecords($ar, $period)) ?></b></td>
            <td class="text-center bg-light"><b><?= minutesToHours(AttendanceRange::addTotalPTOAndMinutes(AttendanceRange::getInternRecords($ar, $period))) ?></b></td>
        </tr>
        <tr>
            <td colspan="2"><b><?= count(User::getAllEmployees()) ?> Employee<?= count(User::getAllEmployees()) > 1 ? 's' : '' ?></b></td>
            <th class="text-right">
                Total:
            </th>
            <td class="text-center bg-light"><b><?= getTotalPaidHours(AttendanceRange::addTotalMinutes($ar)) ?></b></td>
            <td class="text-center bg-light"><b><?= minutesToHours(AttendanceRange::addTotalPTO($ar)) ?></b></td>
            <td class="text-center bg-light"><b><?= AttendanceRange::addTotalNonPaidHours($ar) ?></b></td>
            <td class="text-center bg-light border border-dark"><b><?= minutesToHours(AttendanceRange::addTotalPTOAndMinutes($ar)) ?></b></td>
        </tr>
        <tr>
            <td colspan="8" class="text-center text-muted py-2 service-team"><span><u><b><i>Service Team</i></b></u> - <?= $settings->getServiceTeamPhone() ?> or <u><i>Toll Free</i></u> - <?= $settings->getServiceTeamTollFree() ?></span></td>
        </tr>
    </tbody>
</table>
<form id="frm_finalize_payroll" action="finalize_payroll.php" method="get" target="_blank" class="float-right">
    <div class="form-group">
        <input type="hidden" name="start_date" value="<?= $start_date ?>">
        <input type="hidden" name="end_date" value="<?= $end_date ?>">
        <input type="submit" value="Finalize Payroll" class="btn btn-secondary" id="btn_finalize_payroll">
    </div>
</form>
