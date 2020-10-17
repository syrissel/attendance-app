<?php
require_once('../classes/classes.php');
require('authenticate.php');

$is_date_empty = empty($_GET['date_range']);
$is_id_empty = empty($_GET['id']);

if (isset($_GET['id']) && isset($_GET['date_range']) && !$is_date_empty && !$is_id_empty) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $date_range = filter_input(INPUT_GET, 'date_range', FILTER_SANITIZE_STRING);

    $user = User::findByID($id);

    if (strpos($date_range, '_') > 0) {
        $start_date = substr($date_range, 0, strpos($date_range, '_'));
        $end_date = substr($date_range, strpos($date_range, '_') + 1);
    } else {
        $start_date = substr($date_range, 0, strpos($date_range, ' '));
        $end_date = trim(substr($date_range, strpos($date_range, ' ', strpos($date_range, ' ') + 1)));
    }


    $start = new DateTime($start_date);
    $end = new DateTime($end_date);

    // Does not include end date.
    $end = $end->modify('+1 day');
    $period = new DatePeriod($start, new DateInterval('P1D'), $end);
    $attendance_range = AttendanceRange::create($period, $user);

    $array = [];
    foreach ($period as $date) {
        array_push($array, Attendance::createWithDate($user, $date));
    }
} else {
    $date_range_err = '';
    $id_err = '';

    if (empty(trim($_GET['date_range']))){
        $date_range_err = 'Please select a two week period.';
    }

    if (empty(trim($_GET['id']))){
        $id_err = 'Please select an employee';
    }

    if (!empty($date_range_err) || !empty($id_err)) {
        header("Location: report-partials/employee_biweekly.php?user_error=$id_err&date_range_error=$date_range_err");
    }
}
?>

<!doctype html>
<html>
    <head>
        <link rel="stylesheet" href="../css/print.css">
        <style type="text/css" media="print">
            @page { 
                size: landscape;
            }
            body { 
                writing-mode: tb-rl;
                font-size: 14px !important;
                margin: 0px auto !important;
            }
            a {
                display: none !important;
            }
        </style>
    </head>
    <body>
    <?php include('../nav.php') ?>
        <div class="container-fluid mt-4">
            <div class="row">
                    <div class="col-1"></div>
                    <div class="col-10">
                    <div class="d-flex justify-content-center w-100">
                        <u><h4 class="py-3 font-italic align-self-center">
                            Record for <?= $user->getFullName() ?> — <i><?= $start->format('M d, Y') . ' to ' . $end->modify('-1 day')->format('M d, Y') ?></i>
                        </u></h4>
                        <div class="align-self-end ml-auto my-3">
                            <button type="button" class="btn btn-link" id="print_button" onclick="window.print();return false;">Print</button>
                        </div>
                    </div>
                    <table class="table table-bordered table-hover table-striped mx-4">
                        <thead>
                            <tr>
                                <th class="text-center" scope="col">Date</th>
                                <th class="text-center" scope="col">Clock-in</th>
                                <th class="text-center" scope="col">Clock-out</th>
                                <th class="text-center" scope="col" colspan="2">Morning Break</th>
                                <th class="text-center" scope="col" colspan="2">Lunch Break</th>
                                <th class="text-center" scope="col" colspan="2">Afternoon Break</th>
                                <th class="text-center" scope="col" colspan="3">Unpaid Break</th>
                                <th class="text-center" scope="col">Absent Info</th>
                                <th class="text-center" scope="col">PTO</th>
                                <th class="text-center" scope="col">Paid Hours</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($array as $attendance): ?>
                                <?php if (!$attendance->getIntendedDate()): ?>
                                <tr>
                                    <td>
                                        <?= $attendance->getDate()->format('D, M j') ?>
                                        <a href="new_attendance.php?id=<?= $user->getID() ?>&date=<?= $attendance->getDate()->format('Y-m-d') ?>&date_range=<?= $date_range ?>"><i class="fa fa-plus-square text-success text-decoration-none float-right bs-tooltip" data-toggle="tooltip" data-placement="right" title="Add record" aria-hidden="true"></i></a>
                                    </td>
                                    <td colspan="14" class="text-center font-weight-bold">No record</td>
                                </tr>
                                <?php else: ?>
                                <tr>
                                    <td>
                                        <?= $attendance->getDate()->format('D, M j') ?>
                                        <a href="edit_attendance.php?id=<?= $attendance->getID() ?>&date_range=<?= $date_range ?>"><i class="fa fa-pencil text-info float-right bs-tooltip" data-toggle="tooltip" data-placement="right" title="Edit record" aria-hidden="true"></i></a>
                                    </td>
                                    <td class="text-center"><?= formatPunchInTime($attendance->getClockIn()) ?><?= $attendance->getArriveLate() ? '<i class="fa fa-info-circle text-info float-right" aria-hidden="true" data-toggle="tooltip" data-placement="right" title="Set to arrive at '. date('H:i', strtotime($attendance->getArriveLate())) . '"></i>' : '' ?><?= $attendance->getArriveEarly() ? '<i class="fa fa-info-circle text-info float-right" aria-hidden="true" data-toggle="tooltip" data-placement="right" title="Set to arrive at '. date('H:i', strtotime($attendance->getArriveEarly())) . '"></i>' : '' ?></td>
                                    <td class="text-center"><?= formatPunchInTime($attendance->getClockOut()) ?><?= $attendance->getLeaveEarly() ? '<i class="fa fa-info-circle text-info float-right" aria-hidden="true" data-toggle="tooltip" data-placement="right" title="Set to leave at '. date('H:i', strtotime($attendance->getLeaveEarly())) . '"></i>' : '' ?></td>
                                    <td class="text-center"><?= formatPunchInTime($attendance->getMorningOut()) ?></td>
                                    <td class="text-center"><?= formatPunchInTime($attendance->getMorningIn()) ?></td>
                                    <td class="text-center"><?= formatPunchInTime($attendance->getLunchOut()) ?></td>
                                    <td class="text-center"><?= formatPunchInTime($attendance->getLunchIn()) ?></td>
                                    <td class="text-center"><?= formatPunchInTime($attendance->getAfternoonOut()) ?></td>
                                    <td class="text-center"><?= formatPunchInTime($attendance->getAfternoonIn()) ?></td>
                                    <td class="text-center"><?= formatPunchInTime($attendance->getNonPaidOut()) ?></td>
                                    <td class="text-center"><?= formatPunchInTime($attendance->getNonPaidIn()) ?></td>
                                    <td class="text-center"><?= minutesToHours($attendance->getTotalNonPaidMinutes()) ?></td>
                                    <td class="text-center"><?= $attendance->getAbsentReason() ?><?= $attendance->getPartialDay() ? ('<br>' . $attendance->getPartialDay()) : '' ?></td>
                                    <td class="text-center"><?= minutesToHours($attendance->getPTO()) ?></td>
                                    <td class="text-center"><?= ($attendance->getClockOut() || ($attendance->getAbsentReason() && !$attendance->getPartialDay())) ? getTotalPaidHours($attendance->getTotalWorkedMinutes()) : '<p class="m-0 text-warning bs-tooltip" data-delay=100 data-toggle="tooltip" data-placement="right" title="Missing clock-out"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></p>' ?></td>
                                </tr>
                                <?php endif ?>
                            <?php endforeach ?>
                            <tr>
                                <td colspan="2" class="text-center font-weight-bold">Employee Details</td>
                                <td colspan="10"></td>
                                <td colspan="2" class="text-center">Worked Hours:</td>
                                <td class="text-center font-weight-bold"><?= getTotalPaidHours($attendance_range->getTotalMinutes()) ?></td>
                            </tr>
                            <tr>
                                <td class="text-center">Expected Start Time:</td>
                                <td class="text-center font-weight-bold"><?= $user->getExpectedClockIn() ?></td>
                                <td colspan="10"></td>
                                <td colspan="2" class="text-center">PTO:</td>
                                <td class="text-center font-weight-bold"><span class="text-success">+ <?= getTotalPaidHours($attendance_range->getTotalPTO()) ?></span></td>
                            </tr>
                            <tr>
                                <td class="text-center">Expected Leave Time:</td>
                                <td class="text-center font-weight-bold"><?= $user->getExpectedClockOut() ?></td>
                                <td colspan="10"></td>
                                <td colspan="2" class="text-center">Current Payable Hours:</td>
                                <td class="text-center font-weight-bold"><span class=""><?= getTotalPaidHours($attendance_range->getTotalNetMinutes()) ?></span></td>
                            </tr>
                            <tr>
                                <td class="text-center">Expected Biweekly Hours:</td>
                                <td class="text-center font-weight-bold"><?= $user->getExpectedWorkHours() ?></td>
                                <td colspan="10"></td>
                                <td colspan="2" class="text-center">End of Pay Period:</td>
                                <td class="text-center font-weight-bold"><span class=""><?= getTotalPaidHours($attendance_range->getTotalNetEstimatedMinutes()) ?></span></td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" onclick="window.history.back()" class="btn btn-secondary back-button">Back</button>
                </div><!--col-10-->
                <div class="col-1"></div>
            </div>
        </div>
    <?php include('../footer.php') ?>
    <script src="../js/bootstrap-tooltip.js"></script>
    </body>
</html>
