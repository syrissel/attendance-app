<?php
require_once('../classes/classes.php');
require('authenticate.php');

$date_range_err = $pto_err = '';

if ($_POST && isset($_POST['date_range']) && isset($_POST['reason']) && isset($_POST['checked']) && isset($_POST['pto'])) {
    $checked = $_POST['checked'];
    $date_range = trim(filter_input(INPUT_POST, 'date_range', FILTER_SANITIZE_STRING));
    $reason_id = filter_input(INPUT_POST, 'reason', FILTER_SANITIZE_NUMBER_INT);
    $pto_hours = trim(filter_input(INPUT_POST, 'pto', FILTER_SANITIZE_STRING));
    $pto_minutes = $pto_hours * 60;
    $start_date = substr($date_range, 0, strpos($date_range, ' '));
    $end_date = substr($date_range, strpos($date_range, ' ', strpos($date_range, ' ') + 1));
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);

    // Does not include end date.
    $end = $end->modify('+1 day');
    $period = new DatePeriod($start, new DateInterval('P1D'), $end);
    $users = [];
    $errors = [];
    $err_users = [];
    $pto_exp = "/^(([0-9]|1[0-3])(\.\d\d?)?|14(\.00?)?)$/";
    $date_exp = "/(\d{4})-(\d{2})-(\d{2}) to (\d{4})-(\d{2})-(\d{2})/";

    if (!preg_match($pto_exp, $pto_hours)) {
        $pto_err = "Please enter a whole number or a decimal number.";
    }

    if (!preg_match($date_exp, $date_range)) {
        $date_range_err = 'Please enter a date range in the format \'YYYY-MM-DD to YYYY-MM-DD\'.';
    } else if (empty($date_range)) {
        $date_range_err = 'Please enter a date range.';
    }

    if (empty($date_range_err) && empty($pto_err)) {

        foreach ($checked as $c) {
            array_push($users, User::findByID($c));
        }

        foreach ($period as $date) {
            foreach ($users as $user) {
                $attendance = Attendance::createWithDate($user, $date);
                if (!Attendance::exists($user, $date->format('Y-m-d'))) {
                    $attendance = Attendance::insert($date->format('Y-m-d'), $user);
                    $attendance->updatePTO($pto_minutes);
                    $attendance->updateAbsentReasonID($reason_id);
                } else {
                    array_push($errors, ['name' => $user->getFullName(), 'date' => $date->format('Y-m-d'), 'error' => 'already has an attendance record']);
                    array_push($err_users, $user);
                }
            }
        }
}

    if (count($errors) > 0) {
        echo '<div id="errors"><span>There were some errors while updating data:</span>';
        echo '<ul>';
        foreach ($errors as $e) {
            echo '<li>' . $e['name'] . ' '. $e['error'] . ' for '. $e['date'] . '</li>';
        }
        echo '</ul></div>';
    }

    echo '<div id="records_added"><ul>';
    foreach (array_diff($users, $err_users) as $user) {
        echo "<li>Record(s) added for {$user->getFullName()}!</li>";
    }
    echo '</ul></div>';
}
?>

<html>
<head>
    <title>Bulk PTO</title>
</head>
<body>
    <?php include('../nav.php') ?>
    <div class="container mt-3">
        <div class="row">
            <div class="col-3"></div>
            <div class="col-6">
                <div id="server_message" class="alert alert-success d-none" role="alert"></div>
                <div id="server_errors" class="alert alert-warning d-none" role="alert"></div>
                <h4 class="text-info">Add PTO Hours for Multiple Employees</h4>
                <form id="bulk_pto_form" action="bulk_pto.php" method="post">
                    <div class="form-group">
                        <label for="date_range">Select Date(s)</label>
                        <input type="text" name="date_range" id="date_range" autocomplete="off" class="form-control">
                        <span id="date_range_err" class="text-danger"></span>
                    </div>
                    <label for="">Select Employees</label>
                    <div class="float-right">
                        <input type="checkbox" name="chk_check_all" id="chk_check_all">
                        <label for="chk_check_all">Check all</label>
                    </div>
                    <div style="width:100%;height:400px;overflow-y:scroll;" class="border border-dark rounded p-2">
                        <?php foreach(User::getAllEmployees() as $user): ?>
                            <div class="form-check">
                                <input type="checkbox" name="chk_<?= $user->getUsername() ?>" id="chk_<?= $user->getUsername() ?>" class="form-check-input" data-id="<?= $user->getID() ?>">
                                <label for="chk_<?= $user->getUsername() ?>"><?= $user->getFullName() ?></label>
                            </div>
                        <?php endforeach ?>
                    </div>
                    <div class="form-group">
                        <label for="reason">Select a reason</label>
                        <select name="reason" id="reason" class="form-control">
                            <?php foreach(AbsentReason::getAllReasons() as $reason): ?>
                                <option value="<?= $reason->getID() ?>"><?= $reason->getReason() ?></option>
                            <?php endforeach ?>
                        </select>
                        <span id="reason_err" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="pto">PTO Hours</label>
                        <input type="text" name="pto" id="pto" class="form-control" autocomplete="off">
                        <span id="pto_err" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Submit" class="btn btn-primary">
                        <button type="button" onclick="window.history.back()" class="btn btn-secondary">Back</button>
                    </div>
                </form>
            </div>
            <div class="col-3">
                <div class="alert alert-primary alert-dismissible fade show" role="alert">
                    <strong>Info:</strong> To select just one date, double-click on the date in the datepicker.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php include('../footer.php') ?>
</body>
</html>
<script>
$('#date_range').dateRangePicker({
    autoClose: true,
    showShortcuts: false,
    batchMode: true
});

$('#chk_check_all').change(function() {
    if ($(this).is(':checked')) {
        $('input[type=checkbox]').prop('checked', true);
    } else {
        $('input[type=checkbox]').prop('checked', false);
    }
})

$('#bulk_pto_form').submit(function(event) {
    event.preventDefault()

    let flag = false;
    let ptoReg = /^(([0-9]|1[0-3])(\.\d\d?)?|14(\.00?)?)$/;
    let dateReg = /(\d{4})-(\d{2})-(\d{2}) to (\d{4})-(\d{2})-(\d{2})/;
    let ptoStr = document.getElementById('pto').value;
    let isPtoDecimal = ptoStr.indexOf('.') != -1;
    let ptoDecimal = ptoStr.substr(ptoStr.indexOf('.') + 1);
    let checked = [];
    $('input[type=checkbox]:checked').each(function(i) {
        checked.push($(this).data('id'));
    });

    if (!dateReg.exec($('#date_range').val())) {
        flag = true;
        $('#date_range_err').html('Please select a date range in the format \'YYYY-MM-DD to YYYY-MM-DD\'')
    } else if ($('#date_range').val() === '') {
        flag = true;
        $('#date_range_err').html('Please select a date range.')
    } else {
        $('#date_range_err').html('')
    }
    if (!ptoReg.exec($('#pto').val())) {
        flag = true;
        $('#pto_err').html('Must be a whole number or a decimal.')
    } else if ((isPtoDecimal && ptoDecimal != '25') && (isPtoDecimal && ptoDecimal != '5') && (isPtoDecimal && ptoDecimal != '75')) {
        flag = true;
        $('#pto_err').html('Decimal must be in quarter hours.')
    } else {
        $('#pto_err').html('')
    }
    if ($('#reason').val() === '') {
        flag = true;
        $('#reason_err').html('Please select a reason.')
    } else {
        $('#reason_err').html('')
    }

    if (!flag) {
        let url = $(this).attr('action')
        let post = $.post(url, {
            checked:    checked,
            date_range: $('#date_range').val(),
            reason:     $('#reason').val(),
            pto:        $('#pto').val()
        })

        post.done(function(data) {
            let records = $($.parseHTML(data)).filter('#records_added').html();
            let errors = $($.parseHTML(data)).filter('#errors').html();
            $('#server_message').html(records).removeClass('d-none');
            $('#server_errors').html(errors).removeClass('d-none');
            $('.text-danger').html('')
        })
    }
})
</script>
