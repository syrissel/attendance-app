<?php
require_once('classes/classes.php');
require('vendor/autoload.php');
session_start();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$relative_root = $_ENV['ROOT'];

$username = trim($_SESSION['username']);
$password = $clockin_type = "";
$username_err = $password_err = $reason_err = "";

if (isset($_SESSION['clockin_type'])) {
    $clockin_type = $_SESSION['clockin_type'];
}


if (isset($_GET['back'])) {
    header('location: clockin_select.php');
}

if ($_POST) {

    // Get data from previous form.
    if (isset($_POST['clock_radio'])) {
        $clockin_type = filter_input(INPUT_POST, 'clock_radio', FILTER_SANITIZE_STRING);
        $_SESSION['clockin_type'] = $clockin_type;

        if ($clockin_type == 'guest_out') {
            $guest = User::findByUsername($username);
            $guest_attendance = Attendance::createWithUser($guest);
            $guest_attendance->update('clockout');
            $guest->offboard();
            $_SESSION['verified'] = $guest;
            header("Location: {$relative_root}index.php");
        }

    // Process data from login form.
    } else {
        if (empty(trim($_POST["password"]))){
            $password_err = "Please enter your PIN.";
        } 

        if (isset($_POST['reason'])) {
            $reason = filter_input(INPUT_POST, 'reason', FILTER_SANITIZE_NUMBER_INT);
            if (empty(trim($reason))) {
                $reason_err = "Please select a reason.";
            }
        }
        
        if (empty($password_err) && empty($reason_err)) {

            $password = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING));

            if ($user = User::findByUsername($username)) {
                if ($user->verifyPassword($password)) {
                    $_SESSION['verified'] = $user;
                    try {
                        $attendance = Attendance::createWithUser($user);
                        $attendance->update($_SESSION['clockin_type']);
                        if ($reason) {
                            $attendance->updateAbsentReasonID($reason);
                        }
                        header("location: {$relative_root}index.php");
                    } catch (PDOException $e) {
                        print "Error:" . $e->getMessage();
                    }      
                } else {
                    $password_err = "Incorrect PIN.";
                }
            } else {
                $username_err = "There was an issue finding this user.";
            }
        }
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Clock In</title>
    <link rel="stylesheet" type="text/css" href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
    <script src="js/pinpad.js"></script>
    <link rel="stylesheet" href="/css/pinpad.css">
</head>
<body>
    <?php include('nav.php') ?>
    <div class="container mt-3">
        <div class="row">
            <div class="col-sm-auto col-md-2 col-lg-2"></div>
            <div class="col-sm-auto col-md-8 col-lg-8">
                <div class="alert alert-warning text-center py-2 m-0 font-weight-bold"><?= User::findByUsername($_SESSION['username'])->getFirstName() ?><br><?= ClockInType::getClockInType($clockin_type) ?></div>
                
                <form action="login.php" method="post">
                    <?php if ($clockin_type == 'non_paid_out'): ?>
                    <div class="form-group">
                        <label for="reason">Reason</label>
                        <select name="reason" id="reason" class="form-control <?= !empty($reason_err) ? 'is-invalid' : '' ?>">
                            <option value="" selected></option>
                            
                            <?php foreach(AbsentReason::getAllReasons() as $reason): ?>
                                <option value="<?= $reason->getID() ?>"><?= $reason->getReason() ?></option>
                            <?php endforeach ?>
                        </select>
                        <div id="reason_err" class="text-danger"><?= $reason_err ?></div>
                    </div>
                    <?php endif ?>
                    <div class="form-group w-100">
                        <div class="btn-group-vertical w-100">
                        <div class="btn-group">
                            <input type="password" name="password" class="text-center form-control-lg mb-2 w-100" id="code" readonly>
                            
                        </div>
                        <span class="text-danger"><h5><?= $password_err ?></h5></span>
                        <span class="text-danger"><h5><?= $username_err ?></h5></span>
                        <div class="btn-group">
                            <button id="pin1" type="button" class="btn btn-outline-secondary pin_button"><div>1</div></button>
                            <button id="pin2" type="button" class="btn btn-outline-secondary pin_button"><div>2</div></button>
                            <button id="pin3" type="button" class="btn btn-outline-secondary pin_button"><div>3</div></button>
                        </div>
                        <div class="btn-group">
                            <button id="pin4" type="button" class="btn btn-outline-secondary pin_button"><div>4</div></button>
                            <button id="pin5" type="button" class="btn btn-outline-secondary pin_button"><div>5</div></button>
                            <button id="pin6" type="button" class="btn btn-outline-secondary pin_button"><div>6</div></button>
                        </div>
                        <div class="btn-group">
                            <button id="pin7" type="button" class="btn btn-outline-secondary pin_button"><div>7</div></button>
                            <button id="pin8" type="button" class="btn btn-outline-secondary pin_button"><div>8</div></button>
                            <button id="pin9" type="button" class="btn btn-outline-secondary pin_button"><div>9</div></button>
                        </div>
                        <div class="btn-group">
                            <button id="del" type="button" class="btn btn-outline-secondary pin_button"><div><i class="fa fa-window-close" aria-hidden="true"></i></div></button>
                            <button id="pin0" type="button" class="btn btn-outline-secondary pin_button"><div>0</div></button>
                            <button id="go" type="submit" class="btn btn-primary py-2" >Go</button>
                        </div>
                        </div>
                    </div>
                    <button class="btn btn-dark" type="button" onclick="window.history.back()">Back</button>
                </form>
            </div><!--col-4-->
            <div class="col-sm-auto col-md-2 col-lg-2"></div>
        </div><!--#row-->
    </div>
    <?php include('footer.php') ?>
</body>
</html>
