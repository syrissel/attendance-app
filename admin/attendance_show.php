<?php
require_once('../classes/classes.php');
require('authenticate.php');

if (isset($_GET['id']) && isset($_GET['date'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $date = filter_input(INPUT_GET, 'date', FILTER_SANITIZE_STRING);
    $user = User::findByID($id);
    $date_obj = new DateTime($date);
    $attendance = Attendance::createWithDate($user, $date_obj);
}
?>

<?php include('../nav.php') ?>
<body>
    <div class="container">
    <div class="row mt-4">
        <div class="col-3"></div>
            <div class="col-6">
                <h4>Record for <i><?= $user->getFullName() . ' ' . $date_obj->format('M j, Y') ?></i></h4>
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td>Clock In/Out</td>
                            <td><?= $attendance->getClockIn() ? date('H:i:s', strtotime($attendance->getClockIn())) : 'No record' ?></td>
                            <td><?= $attendance->getClockOut() ? date('H:i:s', strtotime($attendance->getClockOut())) : 'No record' ?></td>
                        </tr>
                        <tr>
                            <td>Morning Break</td>
                            <td><?= $attendance->getMorningOut() ? date('H:i:s', strtotime($attendance->getMorningOut())) : 'No record' ?></td>
                            <td><?= $attendance->getMorningIn() ? date('H:i:s', strtotime($attendance->getMorningIn())) : 'No record' ?></td>
                        </tr>
                        <tr>
                            <td>Lunch Break</td>
                            <td><?= $attendance->getLunchOut() ? date('H:i:s', strtotime($attendance->getLunchOut())) : 'No record' ?></td>
                            <td><?= $attendance->getLunchIn() ? date('H:i:s', strtotime($attendance->getLunchIn())) : 'No record' ?></td>
                        </tr>
                        <tr>
                            <td>Afternoon Break</td>
                            <td><?= $attendance->getAfternoonOut() ? date('H:i:s', strtotime($attendance->getAfternoonOut())) : 'No record' ?></td>
                            <td><?= $attendance->getAfternoonIn() ? date('H:i:s', strtotime($attendance->getAfternoonIn())) : 'No record' ?></td>
                        </tr>
                        <tr>
                            <td>Absent?</td>
                            <td><?= $attendance->getAbsentReasonID() ? AbsentReason::findByID($attendance->getAbsentReasonID())->getReason() : 'No record' ?></td>
                            <td><?= $attendance->getPartialDay() ? $attendance->getPartialDay() : '' ?></td>
                        </tr>
                        <tr>
                            <td>PTO</td>
                            <td><?= $attendance->getPTO() ?></td>
                        </tr>
                    </tbody>        
                </table>
            </div>
        <div class="col-3"></div>
    </div>
    </div>
</body>
<script src="../js/attendance-show.js"></script>
