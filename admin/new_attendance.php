<?php
require_once('../classes/classes.php');
require('authenticate.php');

// Load form.
if ($_GET && isset($_GET['id']) && isset($_GET['date']) && isset($_GET['date_range'])) {
    $id = trim(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT));
    $date = trim(filter_input(INPUT_GET, 'date', FILTER_SANITIZE_STRING));
    $date_range = trim(filter_input(INPUT_GET, 'date_range', FILTER_SANITIZE_STRING));
    $form_user = User::findByID($id);
    $today = new DateTime($date);
    $today->setTime(8, 30);
}
$current_page = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
?>

<!doctype html>
<html>
    <head>
        <title>New Attendance</title>
        <link rel="stylesheet" href="../css/bootstrap-clockpicker.css">
    </head>
    <body>
        <?php include('../nav.php') ?>
        <div class="container mt-4">
            
            <div class="row">
                <div class="col-3"></div>
                <div class="col-6">
                    <h5>New record <i><?= date('M j, Y', strtotime($date)) ?></i> for <?= $form_user->getFullName() ?></h5>
                    <hr>
                    <?php include('partials/new_attendance_form_partial.php') ?>
                </div><!--col-6-->
                <div class="col-3"></div>
            </div><!--row-->
        </div><!--container-->
        <?php include('../footer.php') ?>
    </body>
    <script src="../js/attendance-form.js"></script>
    <script src="../js/new-attendance-form.js"></script>
    <script src="../js/bootstrap-clockpicker.min.js"></script>
    <script src="../js/custom-clockpicker.js"></script>
    <script src="../js/new-form.js"></script>
</html>
