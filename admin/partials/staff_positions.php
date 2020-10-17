<?php
require_once('../../classes/classes.php');
require('../authenticate.php');
session_start();

$position_err = "";

if (isset($_POST['clicked']) && isset($_POST['new_position']) && ($_POST['clicked'] == 'add_new')) {
    $position = trim(filter_input(INPUT_POST, 'new_position', FILTER_SANITIZE_STRING));

    if (empty($position)) {
        $position_err = "Please enter a staff position.";
    } else if (strlen($position) > 20) {
        $position_err = "Cannot be more than 20 characters.";
    }

    if (empty($position_err)) {
        try {
            UserPosition::addPosition($position);
        } catch (PDOException $e) {
            echo '<div id="server-error"><h5 class="text-danger">' . $e->getMessage() . '</h5></div>';
        }
    }

} else if (isset($_POST['clicked']) && isset($_POST['checked']) && ($_POST['clicked'] == 'delete')) {
    $checked = $_POST['checked'];
    $positions_with_errors = [];

    foreach ($checked as $c) {
        try {
            $position = UserPosition::findByID($c);

            if ($position->check()) {
                $last_position = $position;
                array_push($positions_with_errors, $position);
                //$users = User::findByPosition($position->getID());
                //$position_err = '<br />Cannot delete entry since it\'s currently in use by a user. If you want to delete this position, please change the position of the following users:';
            } else {
                $position->destroy();
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
<h4 class="text-info mb-4">Edit Staff Positions</h4>
<form id="staff_positions_form" action="partials/staff_positions.php" method="post">
<?php foreach(UserPosition::getAllPositions() as $position): ?>
    <div class="form-check">    
        <label class="w-100" for="<?= $position->getPosition() ?>">
            <div class="alert alert-primary"><?= $position->getPosition() ?>
                <input class="float-right checkbox" type="checkbox" name="<?= $position->getPosition() ?>" id="<?= $position->getPosition() ?>" data-id="<?= $position->getID() ?>" data-position="<?= $position->getPosition() ?>">
            </div>
        </label>
    </div>
<?php endforeach ?>
<div class="form-group">
    <input type="button" id="delete_button" name="delete" value="Delete Selected" class="btn btn-danger mt-2">
</div>
<hr />
<div class="form-group mt-4">
    <input type="text" name="position" id="position" class="form-control float-left add-new <?= ($position_err) ? 'is-invalid' : '' ?>" placeholder="Add new position">
    <input type="button" name="add_new" id="add_new_button" value="Go" class="btn btn-primary float-right">
    <input type="hidden" name="selected_positions" id="selected_positions">
    <div class="text-danger"><?= $position_err ?></div>
</div>
</form>

<?php if ($positions_with_errors): ?>
<div style="margin-top:5rem;">
    <h5 class="text-danger">The following staff positions could not be deleted:</h5>
    <ul>
        <?php foreach ($positions_with_errors as $p):  ?>
            <li>
                <span class="text-danger"><?= $p->getPosition() ?></span>
                <ul>
                    <?php foreach (User::findByPosition($p->getID()) as $user): ?>
                        <li><?= $user->getFullName() ?></li>
                    <?php endforeach ?>
                </ul>
            </li>
        <?php endforeach ?>
    </ul>
    <span>Details:</span>
    <ul><li>These could not be deleted because they are in use by existing users. <a href="#" id="migrate_position_button">Please migrate these users to another Staff Position to continue with the deletion</a>.</li></ul>
</div>
<?php endif ?>

<script>
$(document).ready(function() {

    let clickedBtn = $('#add_new_button').attr('name');

    $('#delete_button').click(function() {
        clickedBtn = $('#delete_button').attr('name');
        if (confirm('Are you sure?')) {
            $('#staff_positions_form').submit();
        }
    });

    $('#add_new_button').click(function() {
        $('#staff_positions_form').submit();
    });

    $('#migrate_position_button').click(function() {
        let url = 'partials/migrate_positions.php';
        let get = $.get(url);
        get.done(function(data) {
            $('#user-form-container').html(data);
        });
    });

    $('#staff_positions_form').submit(function(event) {
        event.preventDefault();

        let checked = [];
        $('input[type=checkbox]:checked').each(function(i) {
            checked.push($(this).data('id'));
        });

        let url = 'partials/staff_positions.php';
        let post = $.post(url, {
            new_position:       $('#position').val(),
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
        
        let selected = document.getElementById('selected_positions');
        selected.value = $(this).data('position');
    });
});

</script>