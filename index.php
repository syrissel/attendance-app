<?php
    require_once('classes/classes.php');
    session_start();

    define('EMPLOYEE', 1);
    define('ADMIN', 2);
    define('GUEST', 3);

    if (isset($_GET['clear_session'])) {
        session_unset();
        session_destroy();
    }

    if (isset($_SESSION['verified'])) {
        $verified_user = $_SESSION['verified'];
    }

    $users = User::getAllUsers();

    // For the first time register form.
    if (User::getAllUsers() == null) {
        $types = UserType::getAllTypes(false);
        $path = 'admin/register.php';
    }

    $att_array = [];
    $date_obj = new DateTime();
    $date = $date_obj->format('Y-m-d H:i:s');

    foreach (User::getAllEmployees() as $user) {
        array_push($att_array, Attendance::createWithDate($user, $date_obj));
    }

    foreach (User::getAllEmployees() as $user) {
        $temp_a = Attendance::createWithDate($user, $date_obj);
        $has_absence = ($temp_a->getAbsentReasonID() || $temp_a->getPartialDay());

        if ($has_absence) { break; }
    }

    $settings = Settings::getInstance();
?>

<!DOCTYPE html>
<head>
<link rel="stylesheet" type="text/css" href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="css/print.css">
<style>
    #snackbar {
    visibility: hidden;
    width: 100%;
    height: 100%;

    background-color: rgb(29, 13, 13);
    color: #fff;
    text-align: center;
    border-radius: 2px;
    padding: 16px;
    position: fixed;
    z-index: 9999;
    top: 0px;
    left: 0px;
    font-size: 72px;
    }

    #snackbar.show {
    visibility: visible;
    -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
    animation: fadein 0.5s, fadeout 0.5s 2.5s;
    }

    .vertical-center {
        min-height: 100%;  /* Fallback for browsers do NOT support vh unit */
        min-height: 100vh; /* These two lines are counted as one       */

        display: flex;
        align-items: center;
    }

</style>
</head>
<body>
<?php include('nav.php'); ?>
<div id="front_page_container">
    <div class="container mt-3">
    <div id="index_container">
        <div class="row">
        <div class="col-md-3 col-sm-2 col-lg-3"></div>
        <div class="col-md-6 col-sm-8 col-lg-6">
        <?php if (strip_tags($settings->getNoticeContent())): ?>
            <div class="alert alert-warning" role="alert">
                <?= $settings->getNoticeContent() ?>
            </div>
        <?php endif ?>
        <?php if (User::getAllUsers() == null): ?>
            <div class="card my-5 shadow bg-white">
            <h5 class="card-header">Notice</h5>
            <div class="card-body">
                <h4 class="card-title">Looks like there are no users yet.</h4>
                <a href="#" id="create_user_modal_link" class="btn btn-primary py-2 w-100"> Click here to get started.</a>
            </div>
            </div>
        

        <div id="create_user_modal" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create First Admin User</h5>
                </div>
                <div class="container mb-3">
                    <div id="form-container" class="w-50 mx-auto mt-3">
                        <?php include('admin/partials/register_form.php') ?>
                    </div>
                </div>
                </div>
            </div>
        </div>

        <?php endif ?>

        <ul class="list-group list-unstyled">
            <?php foreach ($users as $user): ?>
            <?php $attendance = Attendance::createWithUser($user); ?>
            <?php if (($user->getUserType()->getID() == GUEST)): ?>
                <li class="user-list-item"><a class="btn btn-success w-100 m-1 p-5" href="clockin_select.php?user=<?=$user->getUsername()?>"><?=$user->getFullName() ?></a></li>  
            <?php else: ?>
                <?php if (!($user->getUserType() == GUEST)): ?>
                    <?php if ($attendance->getClockOut()): ?>
                        <a class="btn btn-dark w-100 m-1 p-5 disabled" href="clockin_select.php?user=<?=$user->getUsername()?>">
                        <li class="user-list-item">
                            <div class="row">
                                <div class="col-2"></div>
                                <div class="col-8">
                                    <?=$user->getFullName()?>
                                </div>
                                <div class="col-2"></div>
                            </div>
                        </li>
                        </a>
                    <?php else: ?>
                        <a class="btn btn-dark w-100 m-1 p-5" href="clockin_select.php?user=<?=$user->getUsername()?>">
                        <li class="user-list-item d-flex justify-content-center align-items-center">
                        <?php if ($user->isOnBreak()): ?>
                            <div class="mr-auto invisible">
                            <b class="text-warning">Break</b>
                            </div>
                        <?php elseif ($user->isIn()): ?>
                        <div class="mr-auto invisible">
                            <b style="color:#40dd54;">In</b>
                            </div>
                        <?php endif ?>
                        <div><?= $user->getFullName() ?></div>
                        <?php if ($user->isOnBreak()): ?>
                            <div class="ml-auto">
                            <b class="text-warning">Break</b>
                            </div>
                        <?php elseif ($user->isIn()): ?>
                        <div class="ml-auto">
                            <b style="color:#40dd54;">In</b>
                            </div>
                        <?php endif ?>
                        </li>
                        </a>
                    <?php endif ?>
                <?php endif ?>
            <?php endif ?>
            <?php endforeach ?>
        </ul>
        </div><!--col-6-->
        <div class="col-md-3 col-sm-2 col-lg-3"></div>
        </div><!--row-->
        
    
    <?php if ($_SESSION['verified']): ?>
        
        <div id="snackbar">
        <div class="vertical-center">
            <div class="mx-auto">
                <?= $verified_user->getFormattedLastClockIn() ?>
            </div>
        </div>
        </div>
        <?php
            session_unset();
            session_destroy();
        ?>
    <?php elseif ($_SESSION['guest']): ?>
        <div id="snackbar"><h1><?= $_SESSION['guest'] ?> at</h1><br /><div id="snack_time"></div></div>
        <?php
            session_unset();
            session_destroy();
        ?>
    <?php endif ?>
    <script>
    window.onload = function showSnackbar() {
        var snackbar = document.getElementById("snackbar");
        snackbar.className = "show";
        setTimeout(function(){ snackbar.className = snackbar.className.replace("show", ""); }, 4000);
    }
    <?php if (User::getAllUsers() == null): ?>
        $('#create_user_modal_link').click(function() {
            $('#create_user_modal').modal('show');
        });
    <?php endif ?>
    </script>
    </div><!--index-container-->
    <hr />
    <div id="status_report">
    <h4 class="text-info mt-4 text-center" >Who's here? - <?= date('M j, Y') ?></h4>
    <div class="table-responsive">
        <table class="table table-striped table-bordered mt-2" style="table-layout: fixed;">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (User::getAllUsers() as $user): ?>
                    <tr>
                    <td><?= $user->getFullName() ?></td>
                    <td><?= $user->getPrettyBuildingStatus() ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div><!--table-responsive-->
    </div><!--status_report-->
    </div><!--container-->
</div><!--front_page_container-->
    <?php include('footer.php'); ?>
    <?php include('guest_login.php'); ?>
</body>

<!-- <script src="js/poll.js"></script> -->
<script>
function load() {
    document.getElementById('report_button').addEventListener('click', function(event) {
        document.getElementById('status_report').style.display = 'block';
        document.getElementById('index_container').style.display = 'none';
        document.getElementById('clock_in_report').style.display = 'none';
    });

    document.getElementById('status_button').addEventListener('click', function(event) {
        document.getElementById('clock_in_report').style.display = 'block';
        document.getElementById('index_container').style.display = 'none';
        document.getElementById('status_report').style.display = 'none';
    });

    document.getElementById('print_status_link').addEventListener('click', printStatusReport, false);
    document.getElementById('print_clock_in_link').addEventListener('click', printClockInReport, false);

    function printStatusReport() {
        document.getElementById('clock_in_report').style.display = 'none';
        window.print();
        document.getElementById('clock_in_report').style.display = 'block';
        document.getElementById('index_container').style.display = 'block';
    }

    function printClockInReport() {
        document.getElementById('status_report').style.display = 'none';
        window.print();
        document.getElementById('status_report').style.display = 'block';
        document.getElementById('index_container').style.display = 'block';
    }
}

document.addEventListener("DOMContentLoaded", load, false);
</script>
