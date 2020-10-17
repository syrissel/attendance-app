<?php
session_start();
require_once('classes/classes.php');
$name_err = "";

if ($_GET && isset($_GET['err'])) {
    $name_err = 'Please enter full name.';
}

if ($_POST && isset($_POST['name'])) {

    if (empty(trim($_POST['name']))) {
        $name_err = "Please enter full name.";
    }
    

    if (empty($name_err)) {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $organization = (isset($_POST['organization'])) ? filter_input(INPUT_POST, 'organization', FILTER_SANITIZE_STRING) : 'None';
        $username = 'guest';
        $pin = '8080';
        $clockin_type = 'clockin';

        $first_name = substr($name, 0, strpos($name, ' '));
        $last_name = substr($name, strpos($name, ' '));
    
        User::addGuestUser($username, $first_name, $last_name, $pin, $organization);
        $new_guest = User::getLastGuestUser();
        $new_guest->setUsername($new_guest->getUsername() . strval($new_guest->getID()));
        $new_guest->setLastName($new_guest->getLastName() . ' (Guest)');
        $new_guest->setUserType('Guest');
        $attendance = Attendance::createWithUser($new_guest);
        $attendance->update($clockin_type);
        $_SESSION['verified'] = $new_guest;
        header("location: index.php");
    }
}
?>

<form id="guest-login-form" class="mx-auto mt-3" action="guest_login.php" method="post">
    <div class="form-group">
        <label>Full Name</label>
        <input type="text" id="name" name="name" class="form-control" autocomplete="off">
        <span class="text-danger"><?= $name_err ?></span>
        <small id="name-help-block" class="form-text text-muted"></small>
        <div id="name-err" class="invalid-feedback"></div>
    </div>
    <div class="form-group">
        <label>Organization (optional)</label>
        <input type="text" id="organization" name="organization" class="form-control" autocomplete="off">
        <span class="help-block"></span>
    </div> 
    <div class="form-group">
        <input type="hidden" name="user_type" value="3">
        <input type="hidden" name="username">
        <input type="submit" name="login_guest" class="btn btn-primary" value="Submit">
        <input type="reset" class="btn btn-default" value="Reset">
    </div>
</form>