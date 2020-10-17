<?php
require_once('../classes/classes.php');
require('authenticate.php');
// Find attendance record and load from values.
if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
    $date_range = isset($_GET['date_range']) ? filter_input(INPUT_GET, 'date_range', FILTER_SANITIZE_STRING) : '';
    $attendance = Attendance::findByID($id);
    $form_user = $attendance->getUser();
    $partial_day = $attendance->getPartialDay();

    $partial_start_time = "";
    $partial_end_time = "";

    if ($partial_day) {
        $partial_start_time = trim(substr($partial_day, 0, strpos($partial_day, ' ')));
        $partial_end_time = trim(substr($partial_day, strpos($partial_day, ' ', strpos($partial_day, ' ') + 1)));
    }
}

$current_page = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
?>

<!doctype html>
<html>
    <head>
        <title>Edit Attendance</title>
        <link rel="stylesheet" href="../css/bootstrap-clockpicker.css">
    </head>
    <body>
        <?php include('../nav.php') ?>
        <div class="container mt-4">
            
            <div class="row">
                <div class="col-3"></div>
                <div class="col-6">
                    <h5>Editing record <i><?= date('M j, Y', strtotime($attendance->getIntendedDate())) ?></i> for <?= $form_user->getFullName() ?></h5>
                    <hr>
                    <?php include('partials/edit_attendance_form.php') ?>
                    <div id="date_range" value="<?= $date_range ?>"></div>
                </div><!--col-6-->
                <div class="col-3"></div>
            </div><!--row-->
        </div><!--container-->
        <?php include('../footer.php') ?>
    </body>
</html>
<script src="../js/attendance-form.js"></script>
<script src="../js/edit-attendance-form.js"></script>
<script src="../js/bootstrap-clockpicker.min.js"></script>
<script src="../js/custom-clockpicker.js"></script>
<script src="../js/edit-form.js"></script>
<script>
$('#delete-attendance').click(function(event) {
    if (confirm('Are you sure?')) {
        $('#spinner').show()
        let url = 'partials/delete_attendance.php';
        let get = $.get(url, {
            id: $(this).data('id'),
            date_range: $('#date_range').val()
        });

        get.done(function(data) {
            $('#spinner').hide()
            $('.container').html(data);
        });

    } else {
        event.preventDefault();
    }
});
</script>
