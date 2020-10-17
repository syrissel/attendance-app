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

$absent = trim($_POST['absent']) == 'true' ? true : false;
$reason_err = '';
$date_format_err = false;

// When the record is found.
if ($_POST && isset($_POST['date']) && isset($_POST['id'])) {
    $date = trim(filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING));
    $id = trim(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
    $date_obj = new DateTime($date);
    $form_user = User::findByID($id);

    $attendance = Attendance::createWithDate($form_user, $date_obj);
    $partial_day = $attendance->getPartialDay();

    $partial_start_time = "";
    $partial_end_time = "";

    if ($partial_day) {
        $partial_start_time = trim(substr($partial_day, 0, strpos($partial_day, ' ')));
        $partial_end_time = trim(substr($partial_day, strpos($partial_day, ' ', strpos($partial_day, ' ') + 1)));
    }

// When the record is submitted.
} else if ($_POST) {
    $id = trim(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
    $clockin = trim(filter_input(INPUT_POST, 'clockin', FILTER_SANITIZE_STRING));
    $clockout = trim(filter_input(INPUT_POST, 'clockout', FILTER_SANITIZE_STRING));
    $morningin = trim(filter_input(INPUT_POST, 'morningin', FILTER_SANITIZE_STRING));
    $morningout = trim(filter_input(INPUT_POST, 'morningout', FILTER_SANITIZE_STRING));
    $lunchin = trim(filter_input(INPUT_POST, 'lunchin', FILTER_SANITIZE_STRING));
    $lunchout = trim(filter_input(INPUT_POST, 'lunchout', FILTER_SANITIZE_STRING));
    $afternoonin = trim(filter_input(INPUT_POST, 'afternoonin', FILTER_SANITIZE_STRING));
    $afternoonout = trim(filter_input(INPUT_POST, 'afternoonout', FILTER_SANITIZE_STRING));
    $absent_id = filter_input(INPUT_POST, 'reason', FILTER_SANITIZE_NUMBER_INT);
    $intended_date = filter_input(INPUT_POST, 'intended_date', FILTER_SANITIZE_STRING);
    $pto = trim(filter_input(INPUT_POST, 'pto', FILTER_SANITIZE_STRING));
    $unpaid_break_out = filter_input(INPUT_POST, 'mid_day_break_from', FILTER_SANITIZE_STRING);
    $unpaid_break_in = filter_input(INPUT_POST, 'mid_day_break_to', FILTER_SANITIZE_STRING);
    $arriving_late = filter_input(INPUT_POST, 'arriving_late', FILTER_SANITIZE_STRING);
    $arriving_early = filter_input(INPUT_POST, 'arriving_early', FILTER_SANITIZE_STRING);
    $leaving_early = filter_input(INPUT_POST, 'leaving_early', FILTER_SANITIZE_STRING);
    $is_partial_day = (trim($_POST['reason_radio']) == 'partial') ? true : false;

    $pto_hours = (strlen($pto) > 0) ? $pto : 0;
    $pto_minutes = $pto_hours * 60;


    $form_user = User::findByID($id);
    $intended_date_obj = new DateTime($intended_date);
    $attendance = Attendance::createWithDate($form_user, $intended_date_obj);
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

    if (empty($absent_id) && !(empty(trim($arriving_late)) || empty(trim($arriving_early)) || empty(trim($unpaid_break_in)) || empty(trim($unpaid_break_out)))) {
        $reason_err = 'Please select a reason. You must specify a reason when using the following fields: Arriving Late, Arriving Early, and Unpaid Break';
    }

    if (!$date_format_err && empty($reason_err)) {

        try {
            // Find attendance record.
            $attendance->updateClockIn($clockin);
            $attendance->updateAttendance('clockout', $clockout);
            $attendance->updateAttendance('morningin', $morningin);
            $attendance->updateAttendance('morningout', $morningout);
            $attendance->updateAttendance('lunchin', $lunchin);
            $attendance->updateAttendance('lunchout', $lunchout);
            $attendance->updateAttendance('afternoonin', $afternoonin);
            $attendance->updateAttendance('afternoonout', $afternoonout);
            $attendance->updateAbsentReasonID($absent_id);
            $attendance->updatePTO($pto_minutes);
            $attendance->updateNonPaidIn($unpaid_break_in);
            $attendance->updateNonPaidOut($unpaid_break_out);
            $attendance->updateLeaveEarly($leaving_early);
            $attendance->updateArriveLate($arriving_late);
            $attendance->updateArriveEarly($arriving_early);
            $verify_attendance = Attendance::verifyFields($attendance, $_POST);

            // If form is submitted from edit_attendance.php, redirect.
            if (isset($_POST['date_range']) &&  isset($_POST['current_page'])) {
                $current_page = filter_input(INPUT_POST, 'current_page', FILTER_SANITIZE_STRING);
                $date_range = filter_input(INPUT_POST, 'date_range', FILTER_SANITIZE_STRING);

                if ($current_page == 'edit_attendance.php') {
                    if (isset($_POST['calendar'])) {
                        header('Location: ../calendar.php');
                    } else {
                        header("Location: ../employee_attendance_report.php?id={$form_user->getID()}&date_range=$date_range");
                    }
                }
            }


            // Check if data was properly updated in DB, if not show user an error message.
            if ($verify_attendance === false) {
                echo '<div id="server-error"><h2 class="text-danger">ERROR</h2><h5 class="text-danger"><b>Something went wrong with updating data.<br/>Please contact the website administrator.</b></h5><span class="text-danger">Details</span><ul><li class="text-danger">One of the attendance attributes did not update properly in the database. See verifyFields method in Attendance class (called from user_attendance_form.php).</li></ul></div>';
            } else {
                echo '<div id="server-success"><h5 class="text-body">Changes saved! <i class="fa fa-check text-success" aria-hidden="true"></i></h5></div>';
            }
        } catch (PDOException $e) {
            echo '<div id="server-error"><h2 class="text-danger">ERROR</h2><h5 class="text-danger">' . $e->getMessage() . '</h5></div>';
        }
    } else if ($date_format_err) {
        echo '<div id="server-error"><span class="text-danger">There was an error inserting data because one of the form fields was not in the correct date format. Please use the format Y-M-D H:M:S <br>Example: 2020-09-29 08:30:00</span></div>';
    } else {
        echo '<div id="server-error"><span class="text-danger>Something went wrong. If the user is absent, please specify a reason.</span></div>';
    }
}
?>
<head>
    <link rel="stylesheet" href="<?= $relative_root ?>css/bootstrap-clockpicker.css">
<style>
#snackbar {
  visibility: hidden;
  min-width: 250px;
  margin-left: -125px;
  background-color: rgb(29, 13, 13);
  color: #fff;
  text-align: center;
  border-radius: 2px;
  padding: 16px;
  position: fixed;
  z-index: 9999;
  left: 50%;
  bottom: 50%;
  font-size: 17px;
}

#snackbar.show {
  visibility: visible;
  -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
  animation: fadein 0.5s, fadeout 0.5s 2.5s;
}

@-webkit-keyframes fadein {
  from {bottom: 0; opacity: 0;} 
  to {bottom: 50%; opacity: 1;}
}

@keyframes fadein {
  from {bottom: 0; opacity: 0;}
  to {bottom: 50%; opacity: 1;}
}

@-webkit-keyframes fadeout {
  from {bottom: 50%; opacity: 1;} 
  to {bottom: 0; opacity: 0;}
}

@keyframes fadeout {
  from {bottom: 50%; opacity: 1;}
  to {bottom: 0; opacity: 0;}
}
</style>
</head>
<h4 class="text-info"><a href="#" data-id="<?= $form_user->getID() ?>" class="text-decoration-none text-info user-link"><?= $form_user->getFullName() ?></a> <i style="font-size:20px;" class="fa fa-angle-double-right" aria-hidden="true"></i> <a data-id="<?= $form_user->getID() ?>" href="#" class="attendances-link text-decoration-none text-info">Attendances</a> <i style="font-size:20px;" class="fa fa-angle-double-right" aria-hidden="true"></i> <?= date('F d, Y', strtotime($date)) ?></h4>
<div class="server-message"></div>
<?php if($attendance->getIntendedDate() === null): ?>
    <h5 id="search-result">No record found <i class="fa fa-times" aria-hidden="true" style="color:#FF0000;font-size:20px;"></i></h5>
    <button id="create-attendance-record" type="button" class="btn btn-primary" data-date="<?= $date ?>" data-id="<?= $form_user->getID() ?>">Create Attendance Record</button>
<?php else: ?>

<div id="search_result">Record found for <?= $form_user->getFullName() ?>! <i class="fa fa-check" aria-hidden="true" style="color:#0eb80b;font-size:20px;"></i></div>
<?php if ($attendance->getAbsentReasonID()): ?>
    <div class="alert alert-primary" role="alert">
        <div class="row">
        <div class="col-2"><i class="fa fa-info-circle" aria-hidden="true"></i></div>
        <div class="col-8">
        <span class="font-weight-bold text-center">
            <?= 'Reason: ' . $attendance->getAbsentReason() . '<br>' ?>
            <?php if ($attendance->getPartialDay()): ?>
                <?= $attendance->getAbsentReason() . ' from ' . $attendance->getPartialDay() ?>
            <?php endif ?>
            <?php if ($attendance->getArriveLate()): ?>
                <?php $arrive = new DateTime($attendance->getArriveLate()) ?>
                <?= "Arriving at {$arrive->format('H:i')}<br>" ?>
            <?php endif ?>
            <?php if ($attendance->getLeaveEarly()): ?>
                <?php $early = new DateTime($attendance->getLeaveEarly()) ?>
                <?= "Leaving at {$early->format('H:i')}<br>" ?>
            <?php endif ?>
            <?php if ($attendance->getNonPaidOut() && $attendance->getNonPaidIn()): ?>
                <?= "Unpaid break from " . date('H:i', strtotime($attendance->getNonPaidOut())) . ' to ' . date('H:i', strtotime($attendance->getNonPaidIn())) . '<br>' ?>
            <?php elseif ($attendance->getNonPaidOut()): ?>
                <?= "Started unpaid break at " . date('H:i', strtotime($attendance->getNonPaidOut())) . '<br>'?>
            <?php endif ?>
            <?php if ($attendance->isFullDay()): ?>
                <span>Away for the whole day.</span>
            <?php endif ?>
            <?php if ($attendance->getPTO() > 0): ?>
                <br>
                <?= 'Paid time off = ' . minutesToHours($attendance->getPTO()) . ' hours.' ?>
            <?php endif ?>
        </span>
        </div>
        <div class="col-2"></div>
        </div><!--row-->
    </div>
<?php endif ?>
    <?php include('edit_attendance_form.php') ?>
<?php endif ?>
<div id="confirmation"></div>
<div class="server-message"></div>
<div id="snackbar">Record saved!</div>

<script src="<?= $relative_root ?>js/attendance-form.js"></script>
<script src="<?= $relative_root ?>js/nav-links.js"></script>
<script src="<?= $relative_root ?>js/edit-attendance-form.js"></script>
<script src="<?= $relative_root ?>js/bootstrap-clockpicker.min.js"></script>
<script src="<?= $relative_root ?>js/custom-clockpicker.js"></script>

<script>
<?php if (isset($_POST['new_attendance'])): ?>
    $('#snackbar').addClass('show');
    setTimeout(function(){ 
        $('#snackbar').removeClass('show');
    }, 3000);
<?php endif ?>


setTimeout(() => {
    $('#search_result').hide();
}, 3000);

$('#delete-attendance').click(function(event) {
    if (confirm('Are you sure?')) {
        $('#spinner').show()
        let url = 'partials/delete_attendance.php';
        let get = $.get(url, {
            id: $(this).data('id')
        });

        get.done(function(data) {
            $('#spinner').hide()
            $('#user-form-container').html(data);
        });

    } else {
        event.preventDefault();
    }
});

$('#user-attendance-form').submit(function(event) {
    let clockIn = $.trim($('#clockin').val());
    let clockout = $.trim($('#clockout').val());
    let flag = true;
    let reason = $.trim($('#reason').val());
    let absent = document.getElementById('absent').checked;
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
            
    if (absent && reason === "") {
        flag = false;
        $('#reason_err').show();
        $('#reason_err').html('Please select a reason.');
        $('#reason').addClass('is-invalid');
    }

    if (dateFlag) {
        flag = false;
        $('.server-message').html('<span class="text-danger"><b>Error:</b> It looks like one of the dates is in the wrong format. Please make sure all dates are in the format YYYY-MM-DD HH:MM:SS - Example: 2020-09-29 08:30:00</span>')
    } else if (clockIn === "" && !$('#absent').is(':checked')) {
        flag = false;
        $('#clockin-err').html('Must not be blank.');
        $('#clockin').addClass('is-invalid');
    } else if ((clockout.length > 0) && (clockIn > clockout)) {
        flag = false;
        $('#clockin-err').html('Clock In time cannot be over Clock Out time.');
        $('#clockin').addClass('is-invalid');
        $('#clockout').addClass('is-invalid');
    } else if ($('#absent').is(':checked') && $('#reason').val() == '') {
        flag = false;
        $('#reason').addClass('is-invalid')
    } else if ($('#absent').is(':checked') && $('#reason').val() && !$('input[name="reason_radio"]:checked').val()) {
        flag = false;
        $('#reason_radio_err').html('Please select either Full Day or Partial Day.')
    } else if ($('#pto_check').is(':checked') && !ptoReg.exec($('#pto').val())) {
        flag = false;
        $('#pto').addClass('is-invalid')
        $('#pto_err').html('Must be either a whole number or rounded to two decimal points.')
    } else if ((isPtoDecimal && ptoDecimal != '25') && (isPtoDecimal && ptoDecimal != '5') && (isPtoDecimal && ptoDecimal != '75')) {
        flag = false;
        $('#pto').addClass('is-invalid')
        $('#pto_err').html('Must only be in quarter hours. Eg. 1.25, 4.5, 6.75')
    }

    if (!flag) { event.preventDefault(); }
    
    if (flag) {
        $('.form-control').removeClass('is-invalid');
        $('.form-control').addClass('is-valid');
        $('#reason_err').hide();
        event.preventDefault();
        $('#spinner').show()


        // let url = $(this).attr('action');
        let url = 'partials/user_attendance_form.php';
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
            created_at:         $('#created_at').val(),
            absent:             document.getElementById('absent').checked,
            reason:             $('#reason').val(),
            intended_date:      $('#intended_date').val(),
            reason_radio:       $('input[name="reason_radio"]:checked').val(),
            pto:                $('#pto').val(),
            mid_day_break_from: $('#mid_day_break_from').val(),
            mid_day_break_to:   $('#mid_day_break_to').val(),
            arriving_late:      $('#arriving_late').val(),
            leaving_early:      $('#leaving_early').val(),
            arriving_early:     $('#arriving_early').val()

        });

        post.done(function(data) {
            $('#spinner').hide()
            $('.server-message').show();
            let serverErrorMessage = $($.parseHTML(data)).filter('#server-error').html();
            let serverSuccessMessage = $($.parseHTML(data)).filter('#server-success').html();

            if (serverErrorMessage != null) {
                $('.form-control').addClass('is-invalid');
                $('.server-message').html(serverErrorMessage);
            } else if (serverSuccessMessage != null) {
                $('.form-control').addClass('is-valid');
                $('.server-message').html(serverSuccessMessage);
            }

            setTimeout(() => {
                if (serverSuccessMessage) {
                    $('.server-message').hide();
                }
                $('.form-control').removeClass('is-valid');
            }, 3000);
        });
    }

});
</script>
