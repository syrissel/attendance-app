<?php
require_once('classes/classes.php');
$settings = Settings::getInstance();

// Get get absolute path of current file.
$root_path = trim(__DIR__);
require($root_path . '/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable($root_path);
$dotenv->load();
$relative_root = $_ENV['ROOT'];
$current_page = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
?>

<head>
    <link rel="stylesheet" href="<?= $relative_root ?>css/daterangepicker.min.css">
    <link rel="stylesheet" href="<?= $relative_root ?>css/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?= $relative_root ?>css/pinpad.css">
    <link rel="stylesheet" href="<?= $relative_root ?>css/sidebar.css">
    <?php if ($current_page != 'calendar.php'): ?>
    <script src="<?= $relative_root ?>vendor/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?= $relative_root ?>vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
    <script type="text/javascript" src="<?= $relative_root ?>vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <?php endif ?>
    <script type="text/javascript" src="<?= $relative_root ?>js/moment.min.js"></script>
    <script type="text/javascript" src="<?= $relative_root ?>js/jquery.daterangepicker.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <style>

    </style>
</head>
<nav class="navbar navbar-dark bg-primary" id="main_nav">
  <div class="col-md-4 col-2 col-lg-4">
      <div class="expand navbar-expand" id="navbarSupportedContent">
      <ul class="navbar-nav justify-content-start">
          <?php if ($_SESSION['admin']): ?>
            <?php if (isOS931()): ?>
            <li class="nav-item"><a onclick="showSpinner()" class="btn btn-success mr-2" href="<?= $relative_root ?>admin/register.php">Add User</a></li>
            <li class="nav-item">
              <a href="logout.php" class="btn btn-danger">Logout</a>
            </li>
            <?php else: ?>
          <li class="nav-item" id="btn_sidebar">
          <a class="nav-link p-0" href="javascript:void(0)" id="navbarDropdown" role="button">
            <h1 class="m-0" id="menu_icon"><i class="fa fa-bars" aria-hidden="true"></i></h1>
          </a>
        </li>
        <?php endif ?>
      <?php else: ?>
        <li class="nav-item" id="btn_sidebar">
          <a class="nav-link p-0" href="javascript:void(0)" id="navbarDropdown" role="button">
          <h1 class="m-0" id="menu_icon"><i class="fa fa-bars" aria-hidden="true"></i></h1>
          </a>
        </li>
      <?php endif ?>
      <?php 
        if (($_SERVER["PHP_SELF"] == ($relative_root . 'index.php')) && (User::getAllUsers() != null)) {
          echo '<li class="nav-item my-1 px-4"><a class="btn btn-light mr-2" id="login_guest_link" href="#">Guest Sign-in</a></li>';
        }
        
      ?>

      </ul>
      </div>
  </div>

  <div class="col-md-4 col-8 col-lg-4">
    <div class="mx-auto text-center pt-2" style="color:#FFF;" id="time"></div>
  </div>
  <div class="col-md-4 col-2 col-lg-4 d-flex justify-content-end">
    <div class="d-none d-sm-block m-0 p-0">
      <a class="navbar-brand" href="<?= $relative_root ?>index.php?clear_session=true"><?= $settings->getSiteLogo() ?></a>
    </div>
  </div>
</nav>
<div class="row w-100" style="height:65px;"></div>
<?php include('sidebar.php') ?>

<script src="<?= $relative_root ?>js/clock-widget.js"></script>
<script src="<?= $relative_root ?>js/application.js"></script>
<?php if (!isOS931()): ?>
  <script src="<?= $relative_root ?>js/desktop-application.js"></script>
<?php endif ?>
<script>
$('#login_guest_link').click(function(event) {
  event.preventDefault()
  $('#login_guest_modal').modal('show');
})
</script>
