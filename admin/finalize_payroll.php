<?php
require_once('../classes/classes.php');
require('authenticate.php');
$settings = Settings::getInstance();

if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start_date = filter_input(INPUT_GET, 'start_date', FILTER_SANITIZE_STRING);
    $end_date = filter_input(INPUT_GET, 'end_date', FILTER_SANITIZE_STRING);

    $employees = User::getAllEmployees('', 'user_position_id, payroll_id ASC, first_name');
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);

    // Does not include end date.
    $end = $end->modify('+1 day');
    $period = new DatePeriod($start, new DateInterval('P1D'), $end);

    $ar = [];

    foreach ($employees as $user) {
        $attendance_range = AttendanceRange::create($period, $user);
        
        array_push($ar, $attendance_range);
    }
}
?>
<?php include('../nav.php') ?>
<head>
    <link rel="stylesheet" href="../css/print.css">
</head>
<div class="container">

        <table class="table table-borderless table-striped table-hover">
            <thead>
                <tr>
                    <th class="text-center" colspan="8">
                        <div class="d-flex">
                        <u><h4 class="py-3 font-italic">
                            Payroll
                            <?= date('F d, Y', strtotime($start_date)) . ' - ' . date('F d, Y', strtotime($end_date))  ?>
                        </u></h4>
                        <div class="ml-auto my-3">
                            <button type="button" id="print_button" class="btn btn-link" onclick="window.print();return false;">Print</button>
                            <button type="button" id="help_button" class="btn btn-link">Help</button>
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
                            <td colspan="2"><b><a class="text-body" target="_blank" href="employee_attendance_report.php?id=<?= $a->getUser()->getID() ?>&date_range=<?= $start_date . '_' . $end_date ?>"><?= $a->getUser()->getFullName() ?></a></b></td>
                            <td><?= $a->getUser()->getUserPosition()->getPosition() ?></td>
                            <td class="text-center"><?= getTotalPaidHours($a->getTotalEstimatedMinutes()) ?></td>
                            <td class="text-center"><?= minutesToHours($a->getTotalPTO()) ?></td>
                            <td class="text-center"><?= $a->getEstimatedNonPaidHoursDifference() ?></td>
                            <td class="text-center font-weight-bold"><?= minutesToHours($a->getTotalNetEstimatedMinutes()) ?></td>
                            <td><input type="text" name="comments" class="comments w-100" style="border:none;"></td>
                        </tr>
                    <?php endforeach ?>
                <tr>
                    <td colspan="2"><b><?= count(User::getAdmins()) ?> Full-time</b></td>
                    <th class="text-right">
                        Full-time Hours:
                    </th>
                    <td class="text-center bg-light"><b><?= getTotalPaidHours(AttendanceRange::addTotalEstimatedMinutes(AttendanceRange::getFullTimeRecords($ar, $period))) ?></b></td>
                    <td class="text-center bg-light"><b><?= minutesToHours(AttendanceRange::addTotalPTO(AttendanceRange::getFullTimeRecords($ar, $period))) ?></b></td>
                    <td class="text-center bg-light"><b><?= AttendanceRange::addTotalEstimatedNonPaidHours(AttendanceRange::getFullTimeRecords($ar, $period)) ?></b></td>
                    <td class="text-center bg-light"><b><?= minutesToHours(AttendanceRange::addTotalNetEstimatedMinutes(AttendanceRange::getFullTimeRecords($ar, $period))) ?></b></td>
                </tr>
                <tr>
                    <td colspan="2"><b><?= count(User::getAllInterns()) ?> Intern<?= count(User::getAllInterns()) > 1 ? 's' : '' ?></b></td>
                    <th class="text-right">
                        Intern Hours:
                    </th>
                    <td class="text-center bg-light"><b><?= getTotalPaidHours(AttendanceRange::addTotalEstimatedMinutes(AttendanceRange::getInternRecords($ar, $period))) ?></b></td>
                    <td class="text-center bg-light"><b><?= minutesToHours(AttendanceRange::addTotalPTO(AttendanceRange::getInternRecords($ar, $period))) ?></b></td>
                    <td class="text-center bg-light"><b><?= AttendanceRange::addTotalEstimatedNonPaidHours(AttendanceRange::getInternRecords($ar, $period)) ?></b></td>
                    <td class="text-center bg-light"><b><?= minutesToHours(AttendanceRange::addTotalNetEstimatedMinutes(AttendanceRange::getInternRecords($ar, $period))) ?></b></td>
                </tr>
                <tr>
                    <td colspan="2"><b><?= count(User::getAllEmployees()) ?> Employee<?= count(User::getAllEmployees()) > 1 ? 's' : '' ?></b></td>
                    <th class="text-right">
                        Total:
                    </th>
                    <td class="text-center bg-light"><b><?= getTotalPaidHours(AttendanceRange::addTotalEstimatedMinutes($ar)) ?></b></td>
                    <td class="text-center bg-light"><b><?= minutesToHours(AttendanceRange::addTotalPTO($ar)) ?></b></td>
                    <td class="text-center bg-light"><b><?= AttendanceRange::addTotalEstimatedNonPaidHours($ar) ?></b></td>
                    <td class="text-center bg-light border border-dark"><b><?= minutesToHours(AttendanceRange::addTotalNetEstimatedMinutes($ar)) ?></b></td>
                </tr>
                <tr>
                    <td colspan="8" class="text-center text-muted py-2 service-team"><span><u><b><i>Service Team</i></b></u> - <?= $settings->getServiceTeamPhone() ?> or <u><i>Toll Free</i></u> - <?= $settings->getServiceTeamTollFree() ?></span></td>
                </tr>
            </tbody>
        </table>
        
        <div id="breakdown">
            <h4>Breakdown</h4>
            <hr>
            <?php foreach($ar as $range): ?>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th id="<?= $range->getUser()->getUsername() ?>" class="text-center"><?= $range->getUser()->getFullName() ?></th>
                        <th class="text-center">Clock-in</th>
                        <th class="text-center">Clock-out</th>
                        <th class="text-center">Projected</th>
                        <th class="text-center">Current</th>
                        <th class="text-center">PTO</th>
                        <th class="text-center">Arriving Early</th>
                        <th class="text-center">Arriving Late</th>
                        <th class="text-center">Leaving Early</th>
                        <th class="text-center">Info</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($range->getAttendances() as $a): ?>
                    <tr>
                        <?php
                            $arrive_early = new DateTime($a->getArriveEarly());
                            $arrive_late = new DateTime($a->getArriveLate());
                            $leave_early = new Datetime($a->getLeaveEarly());
                            $clock_in = new DateTime($a->getClockIn());
                            $clock_out = new DateTime($a->getClockOut());
                            $unpaid_in = new DateTime($a->getNonPaidIn());
                            $unpaid_out = new DateTime($a->getNonPaidOut());
                        ?>
                        <td class=""><?= $a->getDate()->format('D, M j') ?></td>
                        <td class="text-center"><?= $a->getClockIn() ? "<b>{$clock_in->format('H:i')}</b>" : 'None' ?></td>
                        <td class="text-center"><?= $a->getClockOut() ? "<b>{$clock_out->format('H:i')}</b>" : 'None' ?></td>
                        <td class="text-center"><?= getTotalPaidHours($a->getTotalEstimatedMinutes()) > 0 ? "<b>". getTotalPaidHours($a->getTotalNetEstimatedMinutes()) . "</b>" : getTotalPaidHours($a->getTotalNetEstimatedMinutes()) ?></td>
                        <td class="text-center"><?= getTotalPaidHours($a->getTotalPaidMinutes()) > 0 ? "<b>". getTotalPaidHours($a->getTotalNetMinutes()) ."</b>" : getTotalPaidHours($a->getTotalNetMinutes()) ?></td>
                        <td class="text-center"><?= minutesToHours($a->getPTO()) > 0 ? "<b>". minutesToHours($a->getPTO()) ."</b>" : minutesToHours($a->getPTO()) ?></td>
                        <td class="text-center"><?= $a->getArriveEarly() ? "<b>{$arrive_early->format('H:i')}</b>" : 'No' ?></td>
                        <td class="text-center"><?= $a->getArriveLate() ? "<b>{$arrive_late->format('H:i')}</b>" : 'No' ?></td>
                        <td class="text-center"><?= $a->getLeaveEarly() ? "<b>{$leave_early->format('H:i')}</b>" : 'No' ?></td>
                        <td class="text-center"><?= $a->getAbsentReason() ? "<b>{$a->getAbsentReason()}</b>" : '' ?></td>
                    </tr>
                    <?php endforeach ?>
                    <tr>
                        <td class="font-weight-bold" colspan="3">Totals:</td>
                        <td class="text-center font-weight-bold"><?= getTotalPaidHours($range->getTotalEstimatedMinutes()) ?></td>
                        <td class="text-center font-weight-bold"><?= getTotalPaidHours($range->getTotalGrossMinutes()) ?></td>
                        <td class="text-center font-weight-bold text-success">+<?= minutesToHours($range->getTotalPTO()) ?></td>
                        <td colspan="4"></td>
                    </tr>
                    <tr>
                        <td class="text-left font-weight-bold">Expected Start/End Times:</td>
                        <td class="text-center font-weight-bold"><?= $range->getUser()->getExpectedClockIn() . '/' . $range->getUser()->getExpectedClockOut() ?></td>
                        <td colspan="4"></td>
                        <td colspan="3" class="text-left font-weight-bold">Total projected paid hours: </td>
                        <td class="text-center font-weight-bold"><?= getTotalPaidHours($range->getTotalNetEstimatedMinutes()) ?></td>
                    </tr>
                    <tr>
                        <td class="text-left font-weight-bold">Expected Biweekly Hours:</td>
                        <td class="text-center font-weight-bold"><?= $range->getUser()->getExpectedWorkHours() ?></td>
                        <td colspan="4"></td>
                        <td colspan="3" class="text-left font-weight-bold">Total current paid hours: </td>
                        <td class="text-center font-weight-bold"><?= getTotalPaidHours($range->getTotalNetMinutes()) ?></td>
                    </tr>
                </tbody>
            </table>
            <?php endforeach ?>
        </div>

        <div id="jump_to_user" class="position-fixed">
            <div class="d-flex flex-column py-2 px-3 border rounded shadow bg-light">
                <h6 class="text-center"><u>Jump to User</u></h6>
                <?php foreach(User::getAllEmployees('limit 25') as $user): ?>
                    <a href="#<?= $user->getUsername() ?>"><?= $user->getFullName() ?></a>
                <?php endforeach ?>
                <a href="#" class="text-primary text-center">Top <i class="fa fa-caret-up" aria-hidden="true"></i></a>
            </div>
        </div>
</div>
<?php include('../footer.php') ?>
<?php include('report-partials/payroll_estimate_help.php') ?>
<script>
$(document).ready(function() {
    $('#help_button').click(function() {
        $('#modal_payroll_estimate_help').modal('show')
    })
})
</script>
<script>
let comments = $('.comments');

comments.each(function(index) {
    if (index % 2 == 0) {
        $(this).css("background", "#F2F2F2");
    }
})
</script>
