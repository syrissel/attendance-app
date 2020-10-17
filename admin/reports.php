<?php
require_once('../classes/classes.php');
require('authenticate.php');
session_start();
$settings = Settings::getInstance();

if (isset($_POST) && isset($_POST['date'])) {
    $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
    try {
        //$array = Attendance::getDayReport($date);
        $att_array = [];
        $date_obj = new DateTime($date);
        $days = [$date];

        foreach (User::getAllEmployees() as $user) {
            $temp_a = Attendance::createWithDate($user, $date_obj);
            $has_absence = ($temp_a->getAbsentReasonID() || $temp_a->getPartialDay());
            array_push($att_array, $temp_a);
        }

        foreach (User::getAllEmployees() as $user) {
            $temp_a = Attendance::createWithDate($user, $date_obj);
            $has_absence = ($temp_a->getAbsentReasonID() || $temp_a->getPartialDay());

            if ($has_absence) { break; }
        }

        //print_r($ar_array);

    } catch (PDOException $e) {
        print "Error :" . $e->getMessage(); 
    }
    
} else if ($_POST && isset($_POST['weekly_report_date'])) {
    $daterange = filter_input(INPUT_POST, 'weekly_report_date', FILTER_SANITIZE_STRING);
    $start_date = trim(substr($daterange, 0, strpos($daterange, ' ')));
    $end_date = trim(substr($daterange, strpos($daterange, ' ', strpos($daterange, ' ') + 1)));

    try {
        $array = Attendance::getWeeklyReport($start_date, $end_date);
    } catch (PDOException $e) {
        print "Error :" . $e->getMessage(); 
    }
} else if ($_POST && isset($_POST['attendance'])) {
    $daterange = filter_input(INPUT_POST, 'attendance', FILTER_SANITIZE_STRING);

    try {
        
        $formatted_start_date = date('F', strtotime(trim(substr($daterange, 0, strpos($daterange, ' ')))));
        $formatted_end_date = date('F', strtotime(trim(substr($daterange, strpos($daterange, ' ', strpos($daterange, ' ') + 1)))));
        $start_date = substr($daterange, 0, strpos($daterange, ' '));
        $end_date = substr($daterange, strpos($daterange, ' ', strpos($daterange, ' ') + 1));
        $user_array = User::getAllEmployees();

        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
    
        // Does not include end date.
        $end = $end->modify('+1 day');
        $period = new DatePeriod($start, new DateInterval('P1D'), $end);

        // To add second week: consider setting $end_date to the end date of second week.
        //$days_array = Attendance::getDatesFromRange($daterange);
        $array = [];
        foreach ($user_array as $user) {
            //array_push($array, Attendance::getUserAttendance($user, $daterange));
            array_push($array, AttendanceRange::create($period, $user));
        }

    } catch (PDOException $e) {
        print "Error :" . $e->getMessage(); 
    }
} else if ($_POST && isset($_POST['guest_date'])) {
    $date = $_POST['guest_date'];
    $daterange = filter_input(INPUT_POST, 'guest_date', FILTER_SANITIZE_STRING);
    $start_date = substr($daterange, 0, strpos($daterange, ' '));
    $end_date = substr($daterange, strpos($daterange, ' ', strpos($daterange, ' ') + 1));
    $guests = User::getAllGuests();
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);

    // Does not include end date.
    $end = $end->modify('+1 day');
    $period = new DatePeriod($start, new DateInterval('P1D'), $end);

    $array = [];

    foreach ($period as $date) {
        array_push($array, UserRange::create($guests, $date));
    }
} else if ($_POST && isset($_POST['employee_date'])) {
    $daterange = filter_input(INPUT_POST, 'employee_date', FILTER_SANITIZE_STRING);
    $start_date = substr($daterange, 0, strpos($daterange, ' '));
    $end_date = substr($daterange, strpos($daterange, ' ', strpos($daterange, ' ') + 1));
    $employees = User::getAllEmployees('', 'user_position_id, payroll_id ASC, first_name');
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);

    // Does not include end date.
    $end = $end->modify('+1 day');
    $period = new DatePeriod($start, new DateInterval('P1D'), $end);

    $ar = [];
    $ar_err = [];

    foreach ($employees as $user) {
        //$user->setTotalBiWeeklyMinutes($end_date);
        $attendance_range = AttendanceRange::create($period, $user);
        if (!empty($attendance_range->getError())) {
            array_push($ar_err, $user);
        }

        array_push($ar, $attendance_range);
    }

} else if ($_POST && isset($_POST['employee_hours_date'])) {
    $daterange = filter_input(INPUT_POST, 'employee_hours_date', FILTER_SANITIZE_STRING);
    $start_date = substr($daterange, 0, strpos($daterange, ' '));
    $end_date = substr($daterange, strpos($daterange, ' ', strpos($daterange, ' ') + 1));
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);

    // Does not include end date.
    $end = $end->modify('+1 day');
    $period = new DatePeriod($start, new DateInterval('P1D'), $end);

    // AttendanceRange object.
    $ar = [];
    foreach (User::getAllEmployees() as $user) {
        array_push($ar, AttendanceRange::create($period, $user));
    }
} else if ($_POST && isset($_POST['student_date'])) {
    $daterange = filter_input(INPUT_POST, 'student_date', FILTER_SANITIZE_STRING);
    $start_date = substr($daterange, 0, strpos($daterange, ' '));
    $end_date = substr($daterange, strpos($daterange, ' ', strpos($daterange, ' ') + 1));
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);

    // Does not include end date.
    $end = $end->modify('+1 day');
    $period = new DatePeriod($start, new DateInterval('P1D'), $end);

    // AttendanceRange object.
    $student_ar = [];
    foreach (User::getAllStudents() as $user) {
        array_push($student_ar, AttendanceRange::create($period, $user));
    }
}

if ($_GET && isset($_GET['daily'])) {
    $daily = true;
} else if ($_GET && isset($_GET['weekly'])) {
    $weekly = true;
} else if ($_GET && isset($_GET['attendance_report'])) {
    $attendance = true;
} else if ($_GET && isset($_GET['guest_report'])) {
    $guest = true;
} else if ($_GET && isset($_GET['employee'])) {
    $employee = true;
} else if ($_GET && isset($_GET['employee_hours'])) {
    $employee_hours = true;
} else if ($_GET && isset($_GET['student_report'])) {
    $student = true;
}
?>

<!doctype html>
<head>
<link rel="stylesheet" type="text/css" href="../vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" media="print" href="../css/print.css">
<style>
    .btn-primary {
        float: left;
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
    <div class="container mt-4">
    <div class="row">
    <div class="col-sm-2 col-md-3 col-lg-3">
    </div>
    <div class="col-sm-8 col-md-6 col-lg-6 bg-light border rounded py-2 py-3 shadow form-container">
    
    <div id="reports_form_container">
    <?php if ($daily): ?>
    <h4 class="report-title">Single Day Report</h4>
    <hr class="mt-0">
    <div class="w-100 d-flex justify-content-center">
        <form class="report-form" action="reports.php?daily=true" method="post">
            <div class="form-group">
                <label for="date">Select a day</label>
                <div id="daily_report_datepicker_container">
                    <input type="hidden" id="date" class="form-control" name="date">
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
    <?php endif ?>
    <?php if($weekly): ?>
    <h4 class="report-title">Multiple Day Reports</h4>
    <hr class="mt-0">
    <div class="w-100 d-flex justify-content-center">
        <form class="report-form" action="reports.php?weekly=true" method="post">
            <div class="form-group">
                <label for="">Select multiple days</label>
                <div id="weekly_report_datepicker_container">
                    <input type="hidden" id="weekly_report_date" class="form-control" name="weekly_report_date">
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
    <?php endif ?>
    <?php if ($attendance): ?>
    <h4 class="report-title">Attendance Report</h4>
    <hr class="mt-0">
    <div class="w-100 d-flex justify-content-center">
        <form class="report-form" id="attendance-form" action="reports.php?attendance_report=true" method="post" class="mx-auto">
            <div class="form-group">
                <label for="">Select a week</label>
                <div id="datepicker_container">
                    <input type="hidden" id="attendance" class="form-control" name="attendance"></input>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
    <?php endif ?>
    <?php if ($guest): ?>
    <h4 class="report-title">Guests Report</h4>
    <hr class="mt-0">
    <div class="w-100 d-flex justify-content-center">
        <form class="report-form" action="reports.php?guest_report=true" method="post" class="mx-auto">
        <div class="form-group">
                <label for="">Select one or multiple days</label>
                <div id="guest_datepicker_container">
                <input type="hidden" id="guest_date" class="form-control" name="guest_date">
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
    <?php endif ?>
    <?php if ($employee): ?>
    <h4 class="report-title">Payroll</h4>
    <hr class="mt-0">
    <div class="w-100 d-flex justify-content-center">
        <form class="report-form" id="employee-form" action="reports.php?employee=true" method="post" class="mx-auto">
        <div class="form-group">
                <label for="">Select a two week period</label>
                <div id="datepicker_container">
                    <input type="hidden" id="employee_date" class="form-control" name="employee_date">
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
    <?php endif ?>
    <?php if ($employee_hours): ?>
    <h4 class="text-info report-title">Employee Hours</h4>
    <form class="report-form" id="employee-hours-form" action="reports.php?employee_hours=true" method="post" class="mx-auto">
        <div class="form-group">
            <div id="employee_hours_datepicker_container">
            <input type="hidden" id="employee_hours_date" class="form-control" name="employee_hours_date">
            </div>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
    <?php endif ?>
    <?php if ($student): ?>
    <h4 class="report-title">Students Biweekly Report</h4>
    <hr class="mt-0">
    <div class="w-100 d-flex justify-content-center">
        <form class="report-form" id="student_form" action="reports.php?student_report=true" method="post" class="mx-auto">
        <div class="form-group">
                <label for="">Select a two week period</label>
                <div id="student_datepicker_container">
                <input type="hidden" id="student_date" class="form-control" name="student_date">
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
    <?php endif ?>
    </div><!--reports_form_container-->
    </div><!--col-8-->
    <div class="col-sm-2 col-md-3 col-lg-3"></div>
    </div>
    <div class="row mt-4">
    <!--Single day-->
    <?php if ($daily && $att_array): ?>
        <?php include('report-partials/daily.php') ?>
    <?php endif ?>
    <!--Select multiple days-->
    <?php if ($weekly && $array): ?>
        <?php include('report-partials/weekly.php') ?>
    <?php endif ?>
    <!--Attendance-->
    <?php if ($attendance && $array): ?>
        <?php include('report-partials/attendance.php') ?>
    <?php endif ?>
    <!--Guests-->
    <?php if ($guest && $array): ?>
        <?php include('report-partials/guest.php') ?>
    <?php endif ?>
    <!--Payroll-->
    <?php if ($employee && $employees): ?>
        <?php include('report-partials/payroll_help.php') ?>
        <?php include('report-partials/payroll.php') ?>
    <?php endif ?>
    <!--Student hours-->
    <?php if ($student && $student_ar): ?>
        <?php include('report-partials/student.php') ?>
    <?php endif ?>
    </div><!--row mt4-->
    </div><!--container-->
    <?php include('../footer.php') ?>
</body>
</html>
<script src="../js/bootstrap-tooltip.js"></script>
<script src="../js/reports.js"></script>
<!-- Initialize jquery date picker. 
     Credit: longbill/Chunlong
     License: https://github.com/longbill/jquery-date-range-picker/blob/master/LICENSE.txt-->
<script>

$(document).ready(function() {
    $('.date-picker-wrapper .footer').hide();
    $('.default-top').hide();
    $('.custom-top').hide();

    let width = document.getElementsByClassName('date-picker-wrapper')[0].offsetWidth;

    $('.report-form').submit(function() {
        $('#spinner').show()
    })

    $('#help_button').click(function() {
        $('#modal_payroll_help').modal('show')
    })
});

let comments = $('.comments');
let overtime = $('.overtime');

comments.each(function(index) {
    if (index % 2 == 0) {
        $(this).css("background", "#F2F2F2");
    }
})

overtime.each(function(index) {
    if (index % 2 == 0) {
        $(this).css("background", "#F2F2F2");
    }
})

$('#employee_date').dateRangePicker({
    batchMode: 'week-range',
    showShortcuts: false,
    inline:true,
    customTopBar: 'Please select a two week range',
	container: '#datepicker_container',
	alwaysOpen:true,
    minDays: 8,
    maxDays: 15
});
$('#guest_date').dateRangePicker({
    batchMode: false,
    showShortcuts: false,
    inline:true,
    customTopBar: 'Please select a date or multiple dates',
	container: '#guest_datepicker_container',
	alwaysOpen:true
});
$('#student_date').dateRangePicker({
    batchMode: 'week-range',
    showShortcuts: false,
    inline:true,
    customTopBar: 'Please select a two week range',
	container: '#student_datepicker_container',
	alwaysOpen:true,
    minDays: 8,
    maxDays: 15
});
$('#employee_hours_date').dateRangePicker({
    batchMode: 'week',
    showShortcuts: false,
    inline:true,
    customTopBar: 'Please select week',
	container: '#employee_hours_datepicker_container',
	alwaysOpen:true
});
$('#weekly_report_date').dateRangePicker({
    batchMode: false,
    showShortcuts: false,
    inline:true,
    customTopBar: 'Please select a range of days',
	container: '#weekly_report_datepicker_container',
	alwaysOpen:true,
    minDays: 2
});
$('#date').dateRangePicker({
    batchMode: false,
    showShortcuts: false,
    inline:true,
    customTopBar: 'Please select a range of days',
	container: '#daily_report_datepicker_container',
	alwaysOpen:true,
    minDays: 2,
    autoClose: true,
	singleDate : true,
	showShortcuts: false
});
$('#attendance').dateRangePicker({
	autoClose: true,
	format: 'YYYY-MM-DD',
	separator: ' to ',
	language: 'auto',
	startOfWeek: 'sunday',// or monday
	getValue: function()
	{
		return $(this).val();
	},
	setValue: function(s)
	{
		if(!$(this).attr('readonly') && !$(this).is(':disabled') && s != $(this).val())
		{
			$(this).val(s);
		}
	},
	startDate: false,
	endDate: false,
	time: {
		enabled: false
	},
	minDays: 0,
	maxDays: 0,
	showShortcuts: false,
	shortcuts:
	{
		//'prev-days': [1,3,5,7],
		//'next-days': [3,5,7],
		//'prev' : ['week','month','year'],
		//'next' : ['week','month','year']
	},
    customShortcuts : [],
    customTopBar: 'Please select week',
	inline:true,
	container:'#datepicker_container',
	alwaysOpen:true,
	singleDate:false,
	lookBehind: false,
	batchMode: 'week',
	duration: 200,
	stickyMonths: false,
	dayDivAttrs: [],
	dayTdAttrs: [],
	applyBtnClass: '',
	singleMonth: 'auto',
	hoveringTooltip: function(days, startTime, hoveringTime)
	{
		return days > 1 ? days + ' ' + lang('days') : '';
	},
	showTopbar: true,
	swapTime: false,
	selectForward: false,
	selectBackward: false,
	showWeekNumbers: false,
	getWeekNumber: function(date) //date will be the first day of a week
	{
		return moment(date).format('w');
	},
	monthSelect: false,
	yearSelect: false
});

document.getElementsByClassName('date-picker-wrapper')[0].style.display = "none";
</script>
