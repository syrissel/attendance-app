<?php
require_once('../../classes/classes.php');
require('../authenticate.php');
session_start();

$reason_err = "";

if (isset($_POST['clicked']) && isset($_POST['new_reason']) && ($_POST['clicked'] == 'add_new')) {
    $reason = trim(filter_input(INPUT_POST, 'new_reason', FILTER_SANITIZE_STRING));

    if (empty($reason)) {
        $reason_err = "Please enter a absent reason.";
    } else if (strlen($reason) > 30) {
        $reason_err = "Cannot be more than 30 characters.";
    }

    if (empty($reason_err)) {
        try {
            AbsentReason::addReason($reason);
        } catch (PDOException $e) {
            echo '<div id="server-error"><h5 class="text-danger">' . $e->getMessage() . '</h5></div>';
        }
    }

} else if (isset($_POST['clicked']) && isset($_POST['checked']) && ($_POST['clicked'] == 'delete')) {
    $checked = $_POST['checked'];
    $reasons_with_errors = [];

    foreach ($checked as $c) {
        try {
            $reason = AbsentReason::findByID($c);

            if ($reason->check()) {
                array_push($reasons_with_errors, $reason);
                //$reason_err = 'Cannot delete this entry since it\'s currently in use by an attendance record. <a href="#" id="migrate_button">You can choose to migrate these attendance records to another entry</a>.';
            } else {
                $reason->destroy();
            }
        } catch (PDOException $e) {
            echo '<div id="server-error"><h5 class="text-danger">' . $e->getMessage() . '</h5></div>';
        }
    }
}
?>
<head>
 <style>
    .checkbox {
        width: 16px;
        height: 16px;
    }

    .form-check {
        padding-left: 0 !important;
    }

    .add-new {
        width: 85% !important;
    }

    .alert {
        margin-bottom: 0;
    }
 </style>
</head>
<div id="server-message"></div>
<h4 class="text-info mb-4">Edit Absent Reasons</h4>
<form id="absent_reasons_form" action="partials/absent_reasons.php" method="post">
<?php foreach(AbsentReason::getAllReasons() as $reason): ?>
    <div class="form-check">    
        <label class="w-100" for="<?= $reason->getReason() ?>">
            <div class="alert alert-primary"><?= $reason->getreason() ?>
                <input class="float-right checkbox" type="checkbox" name="<?= $reason->getReason() ?>" id="<?= $reason->getReason() ?>" data-id="<?= $reason->getID() ?>" data-reason="<?= $reason->getReason() ?>">
            </div>
        </label>
    </div>
<?php endforeach ?>
<div class="form-group">
    <input type="button" id="delete_button" name="delete" value="Delete Selected" class="btn btn-danger mt-2">
</div>
<hr />
<div class="form-group mt-4">
    <input type="text" name="reason" id="reason" class="form-control float-left add-new <?= ($reason_err) ? 'is-invalid' : '' ?>" placeholder="Add new reason">
    <input type="button" name="add_new" id="add_new_button" value="Go" class="btn btn-primary float-right">
    <input type="hidden" name="selected_reasons" id="selected_reasons">
    <div class="text-danger"><?= $reason_err ?></div>
</div>
</form>
<?php if ($reasons_with_errors): ?>
    <div style="margin-top:5rem;">
        <h5 class="text-danger">The following reasons could not be deleted:</h5>
        <ul>
            <?php foreach ($reasons_with_errors as $r): ?>
                <li><?= $r->getReason() ?></li>
            <?php endforeach ?>
        </ul>
        <span>Details:</span>
        <ul><li>These could not be deleted because they are in use by existing attendance records. <a href="#" id="migrate_button">Please migrate these records to another Absent Reason to continue with the deletion</a>.</li></ul>
    </div>
<?php endif ?>
<script>
$(document).ready(function() {
    let clickedBtn = $('#add_new_button').attr('name');

    $('#delete_button').click(function() {
        clickedBtn = $('#delete_button').attr('name');
        if (confirm('Are you sure?')) {
            $('#absent_reasons_form').submit();
        }
    });

    $('#add_new_button').click(function() {
        $('#absent_reasons_form').submit();
    });

    $('#migrate_button').click(function() {
        let url = 'partials/migrate_reasons.php';
        let get = $.get(url);
        get.done(function(data) {
            $('#user-form-container').html(data);
        });
    });

    $('#absent_reasons_form').submit(function(event) {
        event.preventDefault();

        let checked = [];
        $('input[type=checkbox]:checked').each(function(i) {
            checked.push($(this).data('id'));
        });

        let url = 'partials/absent_reasons.php';
        let post = $.post(url, {
            new_reason:       $('#reason').val(),
            clicked:            clickedBtn,
            checked:            checked
        });

        post.done(function(data) {
            let serverSuccessMessage = $($.parseHTML(data)).filter('#server-success').html();
            let serverErrorMessage = $($.parseHTML(data)).filter('#server-error').html();
            if (serverErrorMessage != null) {
                $('.form-control').addClass('is-invalid');
                $('#server-message').html(serverErrorMessage);
            } else if (serverSuccessMessage != null) {
                $('.form-control').addClass('is-valid');
                $('#server-message').html(serverSuccessMessage);
                setTimeout(() => {
                    $('#server-message').hide();
                }, 2000);
            } 

            $('#user-form-container').html(data);
        });

    });

    $('.form-check').click(function() {
        
        let selected = document.getElementById('selected_reasons');
        selected.value = $(this).data('reason');
    });
});
</script>
