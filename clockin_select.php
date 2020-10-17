<?php
session_start();
require_once('classes/classes.php');

define('EMPLOYEE', 1);
define('ADMIN', 2);
define('GUEST', 3);

$settings = Settings::getInstance();

if (isset($_GET['back'])) {
    header('location: index.php');
}

$username = filter_input(INPUT_GET, 'user', FILTER_SANITIZE_STRING);
$_SESSION['username'] = $username;
$user = User::findByUsername($username);
$attendance = Attendance::createWithUser($user);
$outs = [['clockout', 'Clock Out', '<i class="fa fa-clock-o clock-icon" aria-hidden="true"></i>'], 
         ['morningout', 'Morning Break', '<i class="fa fa-coffee clock-icon" aria-hidden="true"></i>'], 
         ['lunchout', 'Lunch', '<i class="fa fa-cutlery clock-icon" aria-hidden="true"></i>'], 
         ['afternoonout', 'Afternoon Break', '<i class="fa fa-coffee clock-icon" aria-hidden="true"></i>'],
         ['non_paid_out', 'Unpaid Break', '<i class="fa fa-clock-o clock-icon" aria-hidden="true"></i>']];
$ins = ['morningin', 'lunchin', 'afternoonin', 'non_paid_in'];
$date = new DateTime();


// If the user clocking in has already used a break or lunch clock-in, remove it.
if ($attendance->getAfternoonIn()) {
    unset($outs[3]);
}
if ($attendance->getMorningIn()) {
    unset($outs[1]);
}
if ($attendance->getLunchIn()) {
    unset($outs[2]);
}
if ($attendance->getNonPaidIn()) {
    unset($outs[4]);
}
?>

<!DOCTYPE html>
    <head>
        <link rel="stylesheet" type="text/css" href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
        <style>
            .clock-icon {
                font-size: 1.6rem !important;
            }
        </style>
    </head>
    <body class="d-flex flex-column min-vh-100">
    <?php include('nav.php'); ?>
    <div class="container mt-3">
    <div class="row">
        <div class="col-md-3 col-sm-2 col-lg-3"></div>
        <div class="col-md-6 col-sm-8 col-lg-6">
        <?php if (isset($_GET['pwchange'])): ?>
            <div id="pin-change" class="alert alert-warning text-center py-2">PIN has been changed.</div>
        <?php endif ?>
    <form action="login.php" method="post">
    <fieldset class="form-group">
            <?php if (!$attendance->getClockIn() && $user->getUserType() != 'Guest'): ?>
            
                <button type="submit" name="clock_radio" value="clockin" class="btn btn-dark m-1 p-5 w-100">
                <div class="row">
                    <div class="col-2"><i class="fa fa-clock-o clock-icon" aria-hidden="true"></i></div>
                    <div class="col-8">Clock In</div>
                    <div class="col-2"></div>
                </div>
                </button><br />
            <?php elseif ($user->getUserType()->getID() == GUEST): ?>
                <button type="submit" name="clock_radio" value="guest_out" class="btn btn-dark m-1 p-5 w-100">Sign Out</button><br />
            <?php elseif ($attendance->getLunchOut() && !$attendance->getLunchIn()): ?>
                <button type="submit" name="clock_radio" value="lunchin" class="btn btn-dark m-1 p-5 w-100">
                <div class="row">
                    <div class="col-2"><i class="fa fa-home clock-icon" aria-hidden="true"></i></i></div>
                    <div class="col-8">Lunch In</div>
                    <div class="col-2"></div>
                </div>
                </button><br /> 
            <?php elseif ($attendance->getMorningOut() && !$attendance->getMorningIn()): ?>
                <button type="submit" name="clock_radio" value="morningin" class="btn btn-dark m-1 p-5 w-100">
                    <div class="row">
                        <div class="col-2"><i class="fa fa-home clock-icon" aria-hidden="true"></i></i></div>
                        <div class="col-8">Break In</div>
                        <div class="col-2"></div>
                    </div>
                </button><br />
            <?php elseif ($attendance->getAfternoonOut() && !$attendance->getAfternoonIn()): ?>
                <button type="submit" name="clock_radio" value="afternoonin" class="btn btn-dark m-1 p-5 w-100">
                    <div class="row">
                        <div class="col-2"><i class="fa fa-home clock-icon" aria-hidden="true"></i></div>
                        <div class="col-8">Break In</div>
                        <div class="col-2"></div>
                    </div>
                </button><br />
            <?php elseif ($attendance->getNonPaidOut() && !$attendance->getNonPaidIn()): ?>
                <button type="submit" name="clock_radio" value="non_paid_in" class="btn btn-dark m-1 p-5 w-100">
                    <div class="row">
                        <div class="col-2"><i class="fa fa-home clock-icon" aria-hidden="true"></i></div>
                        <div class="col-8">Unpaid Break In</div>
                        <div class="col-2"></div>
                    </div>
                </button><br />
            <?php else: ?>
                <?php foreach ($outs as $clock): ?>
                    <button type="submit" name="clock_radio" value="<?= $clock[0] ?>" class="btn btn-dark m-1 p-5 w-100">
                        <div class="row">
                            <div class="col-2"><?= $clock[2] ?></div>
                            <div class="col-8 mx-auto text-center">
                                <p class="m-0 mx-auto"><?= $clock[1] ?></p>
                            </div>
                            <div class="col-2"></div>
                        </div>
                    </button>
                    <br />
                <?php endforeach ?>
            <?php endif ?>
    </fieldset>
    <p><a class="btn btn-dark float-left" href="clockin_select.php?back=true">Back</a></p>
    <?php if ($user->getUserType() != 'Guest'): ?>
    <p><a class="btn btn-dark float-right" href="pin_change.php?id=<?= $user->getID() ?>">Change PIN</a></p>
    <?php endif ?>
    </form>
    </div><!--col-md-6 col-sm-8 col-lg-6-->
    <div class="col-md-3 col-sm-2 col-lg-3"></div>
    </div><!--row-->
    </div><!--container-->
    
    </body>
    <?php include('footer.php') ?>
</html>

<script>
    setTimeout(() => {
        $('#pin-change').hide();
    }, 3000);
</script>
