<?php
require_once('../../classes/classes.php');
require('../authenticate.php');
session_start();

// Get get absolute path of current file.
$root_path = trim(__DIR__ . '/../../');
require($root_path . '/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable($root_path);
$dotenv->load();
$relative_root = $_ENV['ROOT'];

$clockin_err = "";
$reason_err = '';
$date_format_err = false;

// Load form.
if ($_GET && isset($_GET['id']) && isset($_GET['date'])) {
    $id = trim(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT));
    $date = trim(filter_input(INPUT_GET, 'date', FILTER_SANITIZE_STRING));
    $form_user = User::findByID($id);
    $today = new DateTime($date);
    $today->setTime(8, 30);
}

if ($_POST) {
    $absent = (trim($_POST['absent']) == 'on' || trim($_POST['absent']) == 'true') ? true : false;
    $reason = filter_input(INPUT_POST, 'reason', FILTER_SANITIZE_STRING);
    $reason_id = filter_input(INPUT_POST, 'reason', FILTER_SANITIZE_NUMBER_INT);
    $intended_date = trim(filter_input(INPUT_POST, 'intended_date', FILTER_SANITIZE_STRING));
    $date_obj = new DateTime($intended_date);
    $form_user = User::findByID($id);
    $pto = trim(filter_input(INPUT_POST, 'pto', FILTER_SANITIZE_STRING));
    $unpaid_break_out = filter_input(INPUT_POST, 'mid_day_break_from', FILTER_SANITIZE_STRING);
    $unpaid_break_in = filter_input(INPUT_POST, 'mid_day_break_to', FILTER_SANITIZE_STRING);
    $arriving_late = filter_input(INPUT_POST, 'arriving_late', FILTER_SANITIZE_STRING);
    $leaving_early = filter_input(INPUT_POST, 'leaving_early', FILTER_SANITIZE_STRING);
    $arriving_early = filter_input(INPUT_POST, 'arriving_early', FILTER_SANITIZE_STRING);
    $id = trim(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING));
    $clockin = trim(filter_input(INPUT_POST, 'clockin', FILTER_SANITIZE_STRING));
    $clockout = trim(filter_input(INPUT_POST, 'clockout', FILTER_SANITIZE_STRING));
    $morningin = trim(filter_input(INPUT_POST, 'morningin', FILTER_SANITIZE_STRING));
    $morningout = trim(filter_input(INPUT_POST, 'morningout', FILTER_SANITIZE_STRING));
    $lunchin = trim(filter_input(INPUT_POST, 'lunchin', FILTER_SANITIZE_STRING));
    $lunchout = trim(filter_input(INPUT_POST, 'lunchout', FILTER_SANITIZE_STRING));
    $afternoonin = trim(filter_input(INPUT_POST, 'afternoonin', FILTER_SANITIZE_STRING));
    $afternoonout = trim(filter_input(INPUT_POST, 'afternoonout', FILTER_SANITIZE_STRING));
    $date_obj = new DateTime($intended_date);
    $form_user = User::findByID($id);
    $today = new DateTime($intended_date);
    $pto_hours = (strlen($pto) > 0) ? $pto : 0;
    // Convert pto hours into minutes to be stored in db.
    $pto_minutes = $pto_hours * 60;
    $date_exp = "/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/";

    $times = [];
    array_push($times, $clockin);
    array_push($times, $clockout);
    array_push($times, $morningin);
    array_push($times, $morningout);
    array_push($times, $lunchin);
    array_push($times, $lunchout);
    array_push($times, $afternoonin);
    array_push($times, $afternoonout);
    array_push($times, $unpaid_break_out);
    array_push($times, $unpaid_break_in);
    array_push($times, $arriving_early);
    array_push($times, $arriving_late);

    foreach ($times as $time) {
        if (strlen($time) > 0) {
            if (!preg_match($date_exp, $time)) {
                $date_format_err = true;
            }
        }
    }

    if (empty($reason_id) && !(empty(trim($arriving_late)) || empty(trim($arriving_early)) || empty(trim($unpaid_break_in)) || empty(trim($unpaid_break_out)))) {
        $reason_err = 'Please select a reason. You must specify a reason when using the following fields: Arriving Late, Arriving Early, and Unpaid Break';
    }

    if (empty($clockin) && !$absent) {
        $clockin_err = 'Please select clockin time for ' . $form_user->getUsername() . '.';
    }

    if ($absent && strlen($reason) < 1) {
        $reason_err = true;
        echo '<div id="server_reason_err">Please select a reason.</div>';
    }
    
    // If an attendance record for this day for this user does not exist in the database, create one.
    if (!(Attendance::exists($form_user, $date_obj->format('Y-m-d'))) && !$clockin_err && !$reason_err && !$date_format_err) {

        try {
            $new_attendance = Attendance::insert($intended_date, $form_user);
            $new_attendance->updateAbsentReasonID($reason_id);
            $new_attendance->setAbsentReasonID($reason_id);
            $new_attendance->updateNonPaidIn($unpaid_break_in);
            $new_attendance->updateNonPaidOut($unpaid_break_out);
            $new_attendance->updateLeaveEarly($leaving_early);
            $new_attendance->updateArriveLate($arriving_late);
            $new_attendance->updateArriveEarly($arriving_early);
            $new_attendance->updatePTO($pto_minutes);
            $new_attendance->updateClockIn($clockin);
            $new_attendance->updateAttendance('clockout', $clockout);
            $new_attendance->updateAttendance('morningin', $morningin);
            $new_attendance->updateAttendance('morningout', $morningout);
            $new_attendance->updateAttendance('lunchin', $lunchin);
            $new_attendance->updateAttendance('lunchout', $lunchout);
            $new_attendance->updateAttendance('afternoonin', $afternoonin);
            $new_attendance->updateAttendance('afternoonout', $afternoonout);
        } catch (PDOException $e) {
            echo '<div id="server-error"><h2 class="text-danger">ERROR</h2><h5 class="text-danger"><b>Something went wrong while inserting data. Things are probably fine, try reloading the page and try again. Otherwise please contact the website administrator.</b></h5><span class="text-danger">Details</span><ul><li class="text-danger">'. $e->getMessage() . '</li><li class="text-danger">One of the form fields you entered failed to save in the database.</li><li class="text-danger">Please make sure all of the fields are in the correct date format (Y-M-D H:M:S)</li><li class="text-danger">The record may have been created but without the fields you specified. Please reload this page and try again.</li></ul></div>';
        }
        // If form is submitted from new_attendance.php, redirect.
        if (isset($_POST['date_range']) &&  isset($_POST['current_page'])) {
            $current_page = filter_input(INPUT_POST, 'current_page', FILTER_SANITIZE_STRING);
            $date_range = filter_input(INPUT_POST, 'date_range', FILTER_SANITIZE_STRING);

            if ($current_page == 'new_attendance.php') {
                header("Location: ../employee_attendance_report.php?id={$form_user->getID()}&date_range=$date_range&snackbar=true");
            }
        }

        if (Attendance::verifyFields($new_attendance, $_POST) == false || $reason_id != $new_attendance->getAbsentReasonID()) {
            echo '<div id="server-error"><h2 class="text-danger">ERROR</h2><h5 class="text-danger"><b>Something went wrong while inserting data.<br/>Please contact the website administrator.</b></h5><span class="text-danger">Details</span><ul><li class="text-danger">Please make sure all of the fields are in the correct date format (Y-M-D H:M:S)</li><li class="text-danger">The record may have been created but without the fields you specified. Please reload this page and try again.</li><li class="text-danger">One of the attendance attributes did not update properly in the database. See verifyFields method in Attendance class (called from new_user_attendance_form.php).</li></ul></div>';
        } else {
            echo '<div id="server-success"><h5 class="text-success">Changes saved! <i class="fa fa-check text-success" aria-hidden="true"></i></h5></div>';
        }
    } else if ($date_format_err) {
        echo '<div id="server-error"><span class="text-danger">There was an error inserting data because one of the form fields was not in the correct date format. Please use the format Y-M-D H:M:S <br>Example: 2020-09-29 08:30:00</span></div>';
    } else {
        echo '<div id="server-error"><h2 class="text-danger">ERROR</h2><h5 class="text-danger">Looks like this user already has a record for this date.<br />If you are trying to update any data, please go back and search for this date.</h5></div>';
    }
}
?>
<head>
    <link rel="stylesheet" href="<?= $relative_root ?>css/bootstrap-clockpicker.css">
</head>
<h4 class="text-info"><a href="#" data-id="<?= $form_user->getID() ?>" class="text-decoration-none text-info user-link"><?= $form_user->getFullName() ?></a> <i style="font-size:20px;" class="fa fa-angle-double-right" aria-hidden="true"></i> <a data-id="<?= $form_user->getID() ?>" href="#" class="text-decoration-none text-info attendances-link">Attendances</a> <i style="font-size:20px;" class="fa fa-angle-double-right" aria-hidden="true"></i> New Attendance for <?= date('F d, Y', strtotime($date)) ?> </h4>
<div id="server-message"></div>
<div id="counter"></div>
<?php include('new_attendance_form_partial.php') ?>
<div id="confirmation"></div>

<script src="<?= $relative_root ?>js/attendance-form.js"></script>
<script src="<?= $relative_root ?>js/nav-links.js"></script>
<script src="<?= $relative_root ?>js/new-attendance-form.js"></script>
<script src="<?= $relative_root ?>js/bootstrap-clockpicker.min.js"></script>
<script src="<?= $relative_root ?>js/custom-clockpicker.js"></script>

<script>
$('#new-user-attendance-form').submit(function(event) {
    
    let clockIn = $.trim($('#clockin').val());
    let flag = true;
    let ptoReg = /^(([0-9]|1[0-3])(\.\d\d?)?|14(\.00?)?)$/;
    let dateReg = /(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/;
    let ptoStr = document.getElementById('pto').value;
    let isPtoDecimal = ptoStr.indexOf('.') != -1;
    let ptoDecimal = ptoStr.substr(ptoStr.indexOf('.') + 1);
    let dateFlag = false;
    let times = [$('#clockin').val(), $('#clockout').val(), $('#morningin').val(), $('#morningout').val(), $('#lunchin').val(), $('#lunchout').val(),
                 $('#afternoonin').val(), $('#afternoonout').val(), $('#arriving_early').val(), $('#arriving_late').val(), $('#leaving_early').val(), 
                 $('#mid_day_break_from').val(), $('#mid_day_break_to').val()];

    times.forEach(function(item, index) {
        if (item.length > 0)
            if (!dateReg.exec(item))
                dateFlag = true;
    })

    if (dateFlag) {
        $('#server-message').html('<span class="text-danger"><b>Error:</b> It looks like one of the dates is in the wrong format. Please make sure all dates are in the format YYYY-MM-DD HH:MM:SS - Example: 2020-09-29 08:30:00</span>')
        event.preventDefault()
    } else if (clockIn === "" && !$('#absent').is(':checked')) {
        flag = false;
        $('#clockin-err').html('Must not be blank.');
        $('#clockin').addClass('is-invalid');
        event.preventDefault();
    } else if ((clockout.length > 0) && (clockIn > clockout)) {
        flag = false;
        $('#clockin-err').html('Clock In time cannot be over Clock Out time.');
        $('#clockin').addClass('is-invalid');
        $('#clockout').addClass('is-invalid');
        event.preventDefault();
    } else if ($('#pto_check').is(':checked') && !ptoReg.exec($('#pto').val())) {
        $('#pto').addClass('is-invalid')
        $('#pto_err').html('Must be either a whole number or rounded to two decimal points.')
        event.preventDefault()
    } else if ((isPtoDecimal && ptoDecimal != '25') && (isPtoDecimal && ptoDecimal != '5') && (isPtoDecimal && ptoDecimal != '75')) {
        $('#pto').addClass('is-invalid')
        $('#pto_err').html('Must only be in quarter hours. Eg. 1.25, 4.5, 6.75')
        event.preventDefault()
    } else if ($('#absent').is(':checked') && $('#reason').val() == '') {
        $('#reason').addClass('is-invalid')
        event.preventDefault()
    } else if ($('#absent').is(':checked') && $('#reason').val() && !$('input[name="reason_radio"]:checked').val()) {
        $('#reason_radio_err').html('Please select either Full Day or Partial Day.')
        event.preventDefault()
    } else {
        $('.form-control').removeClass('is-invalid');
        //$('.form-control').addClass('is-valid');
        event.preventDefault();
        $('#spinner').show()
        let url = $(this).attr('action');
        let post = $.post(url, {
            clockin:            $('#clockin').val(),
            clockout:           $('#clockout').val(),
            morningin:          $('#morningin').val(),
            morningout:         $('#morningout').val(),
            lunchin:            $('#lunchin').val(),
            lunchout:           $('#lunchout').val(),
            afternoonin:        $('#afternoonin').val(),
            afternoonout:       $('#afternoonout').val(),
            id:                 $('#id').val(),
            absent:             document.getElementById('absent').checked,
            reason:             $('#reason').val(),
            intended_date:      $('#intended_date').val(),
            reason_radio:       $('input[name="reason_radio"]:checked').val(),
            pto:                $('#pto').val(),
            arriving_late:      $('#arriving_late').val(),
            leaving_early:      $('#leaving_early').val(),
            arriving_early:     $('#arriving_early').val(),
            mid_day_break_from: $('#mid_day_break_from').val(),
            mid_day_break_to:   $('#mid_day_break_to').val()

        });

        post.done(function(data) {
            $('#spinner').hide()
            $('#server-message').show();
            let serverErrorMessage = $($.parseHTML(data)).filter('#server-error').html();
            let serverSuccessMessage = $($.parseHTML(data)).filter('#server-success').html();
            let reasonError = $($.parseHTML(data)).filter('#server_reason_err').html();
            
            if (reasonError) {
                $('#reason_err').html(reasonError);
                $('#reason').addClass('is-invalid');
            } else {
                $('#reason').removeClass('is-invalid');
                $('#reason').addClass('is-valid');
            }
            
            if (serverErrorMessage != null) {
                $('.form-control').addClass('is-invalid');
                $('#server-message').html(serverErrorMessage);
            } else if (serverSuccessMessage != null) {
                $('.form-control').addClass('is-valid');
                $('#server-message').html(serverSuccessMessage);
                setTimeout(() => {
                    $('.form-control').removeClass('is-valid');
                    $('#server-message').hide();
                }, 3000);
            }

            // Set all attendance fields to disabled once the response has been received.
            // $('.attendance-field').attr('disabled', 'disabled');

            // If an item is selected in the select, set the value of all attendance fields to the reason.
            if ($('#reason').prop('selectedIndex') > 0) {
                $('.attendance-field').each(function() {
                    $(this).val($('#reason').children().get($('#reason').prop('selectedIndex')).text);
                })
            }


            if (serverErrorMessage == null) {
                let post = $.post('partials/user_attendance_form.php', {
                    id: $('#id').val(),
                    date: $('#date').val(),
                    new_attendance: true
                });
                post.done(function(data) {
                    $('#user-form-container').html(data)
                })
            }
        });
    }
});
</script>
