<?php
require_once('../classes/classes.php');
require('authenticate.php');
session_start();
$users = User::getAllEmployees();

$start = new DateTime();
$end = new DateTime();

// 2 weeks in the past.
$start->sub(new DateInterval('P2W'));

// Does not include end date.
$end->modify('+1 day');
$period = new DatePeriod($start, new DateInterval('P1D'), $end);

$ar_array = [];

foreach ($users as $user) {
    //array_push($array, Attendance::getTwoWeekLateReport($user));
    array_push($ar_array, AttendanceRange::create($period, $user));
}
?>

<!doctype html>
<head>
    <style>
        @media print {
            .footer {
                display: none;
            }
        }
    </style>
</head>
<body>
    <?php include('../nav.php') ?>
    <div id="spinner" class="position-fixed w-100 text-center" style="display:none; z-index:10;">
        <div class="row" style="height:20rem;"></div>

        <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <div class="container">
    <div class="row">
    <h3 class="text-info my-4 ml-1">Employee Overview</h3>
    <table class="table table-striped table-bordered" style="table-layout: fixed;">
        <thead>
            <tr><td colspan="2"><h4 class="text-info text-center my-2">Who's here?</h4></td></tr>
            <tr>
                <th>Name</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (User::getAllUsers() as $user): ?>
                <tr>
                <td><?= $user->getFullName() ?></td>
                <td><a href="attendance_show.php?id=<?= $user->getID() ?>&date=<?= date('Y-m-d') ?>" target="_blank"><?= $user->getPrettyBuildingStatus() ?></a></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    
    <div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
            <tr><td colspan="16"><h4 class="text-info text-center my-2">Late Report (Past Two Weeks)</h4></td></tr>
            <tr>
            <th>Name</th>
            <?php foreach ($period as $p): ?>
                <th><?= $p->format('D') . ', ' . $p->format('d') ?></th>
            <?php endforeach ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ar_array as $ar):  ?>
                <tr>
                    <td><?= $ar->getUser()->getFullName() ?></td>
                    <?php foreach ($ar->getAttendances() as $a): ?>
                            <td><a href="attendance_show.php?id=<?= $ar->getUser()->getID() ?>&date=<?= $a->getIntendedDate() ?>" target="_blank"><?= $a->getLateStatus() ?></a></td>
                    <?php endforeach ?>
                </tr>
            <?php endforeach  ?>
        </tbody>
    </table>
    </div><!--table-responsive-->

    </div><!--row-->
    </div><!--container-->
    <?php include('../footer.php') ?>
</body>
</html>
<script src="../js/bootstrap-tooltip.js"></script>
