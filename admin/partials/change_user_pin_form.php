<?php
require_once('../../classes/classes.php');
require('../authenticate.php');
session_start();
$new_pin_err = $confirm_pin_err = "";

if ($_GET && isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $user = User::findByID($id);
} else if ($_POST && isset($_POST['new_pin']) && isset($_POST['confirm_pin'])) {
    $new_pin = trim(filter_input(INPUT_POST, 'new_pin', FILTER_SANITIZE_STRING));
    $confirm_pin = trim(filter_input(INPUT_POST, 'confirm_pin', FILTER_SANITIZE_STRING));
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

    if (empty($new_pin)) {
        $new_pin_err = "Please enter a pin.";
    } else if (!((strlen($new_pin) <= 6) && (strlen($new_pin) >= 4))) {
        $new_pin_err = "PIN can be 4 to 6 characters.";
    }
    
    if (empty($confirm_pin)) {
        $confirm_pin_err = "Please confirm the pin.";
    } else if (!((strlen($confirm_pin) <= 6) && (strlen($confirm_pin) >= 4))) {
        $confirm_pin_err = "PIN can be 4 to 6 characters.";
    } else if ($confirm_pin != $new_pin) {
        $confirm_pin_err = "Passwords do not match.";
    }

    if (empty($new_pin_err) && empty($confirm_pin_err)) {
        $user = User::findByID($id);

        // PIN is salted and hashed in the updatePIN method.
        $user->updatePIN($new_pin);
        echo '<h2 class="text-success">Success</h2>';
    }

} else {
    header('../users.php');
}
?>
<h4 class="text-info"><a href="#" class="user-link text-decoration-none text-info" data-id="<?= $user->getID() ?>"><?= $user->getFullName() ?></a> <i style="font-size:20px;" class="fa fa-angle-double-right" aria-hidden="true"></i> Change PIN</h4>
<form id="change_user_pin_form" action="change_user_pin_form.php" method="post">
    <div class="form-group">
        <label for="new_pin">New Pin</label>
        <input class="form-control" type="password" name="new_pin" id="new_pin">
        <div class="text-danger"><span><?= $new_pin_err ?></span></div>
        <div class="invalid-feedback" id="new_pin_err"></div>
    </div>
    <div class="form-group">
        <label for="confirm_pin">Confirm Pin</label>
        <input class="form-control" type="password" name="confirm_pin" id="confirm_pin">
        <div class="text-danger"><span><?= $confirm_pin_err ?></span></div>
        <div class="invalid-feedback" id="confirm_pin_err"></div>
    </div>
    <div class="form-group">
        <input type="hidden" name="id" id="id" value="<?= $id ?>">
        <input class="btn btn-primary" type="submit" value="Submit">
    </div>
</form>

<script src="../../js/nav-links.js"></script>
<script>
$('#change_user_pin_form').submit(function(event) {
    let newPin = $.trim($('#new_pin').val());
    let confirmPin = $.trim($('#confirm_pin').val());
    let valid = true;

    if ((newPin === "") || !((newPin.length >= 4) && (newPin.length <= 6))) {
        $('#new_pin').addClass('is-invalid');
        $('#new_pin_err').html('Please enter a pin of 4 to 6 characters.');
        valid = false;
        event.preventDefault();
    } else if (!isNumber(newPin)) {
        $('#new_pin').addClass('is-invalid');
        $('#new_pin_err').html('PIN must be a number.');
        valid = false;
        event.preventDefault();
    } else {
        $('#new_pin').removeClass('is-invalid');
        $('#new_pin').addClass('is-valid');
    }

    if ((confirmPin === "") || !((confirmPin.length >= 4) && (confirmPin.length <= 6))) {
        $('#confirm_pin').addClass('is-invalid');
        $('#confirm_pin_err').html('Please enter a pin of 4 to 6 characters.');
        valid = false;
        event.preventDefault();
    } else if (!isNumber(confirmPin)) {
        $('#confirm_pin').addClass('is-invalid');
        $('#confirm_pin_err').html('PIN must be a number.');
        valid = false;
        event.preventDefault();
    } else if (newPin !== confirmPin) {
        $('#confirm_pin').addClass('is-invalid');
        $('#confirm_pin_err').html('PINs do not match');
        valid = false;
        event.preventDefault();
    } else {
        $('#confirm_pin').removeClass('is-invalid');
        $('#confirm_pin').addClass('is-valid');
    }

    if (valid) {
        event.preventDefault();

        $('.form-control').removeClass('is-invalid');
        $('.form-control').addClass('is-valid');
        $('#new_pin_err').html('');
        $('#confirm_pin_err').html('');

        let url = 'partials/change_user_pin_form.php';
        let post = $.post(url, {
            id:          $('#id').val(),
            new_pin:     $('#new_pin').val(),
            confirm_pin: $('#confirm_pin').val()
        });

        post.done(function(data) {
            $('#confirmation').html('<h5 class="text-success">PIN has been Changed</h5>');
            setTimeout(() => {
                $('#confirmation').hide();
            }, 2000);
            
        });
    }
});

// Returns true if argument is a string of numbers.
function isNumber(num) {
    let reg = new RegExp('^\\d+$');
    return reg.test(num);
}
</script>
