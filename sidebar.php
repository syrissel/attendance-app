<?php
require_once('classes/classes.php');
$settings = Settings::getInstance();

// Get get absolute path of current file.
$root_path = trim(__DIR__);
require($root_path . '/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable($root_path);
$dotenv->load();
$relative_root = $_ENV['ROOT'];
?>

<div class="sidebar-wrapper shadow">
  <div class="navbar-header">
    <div class="brand-wrapper">
      <div class="brand-name-wrapper">
        <a class="navbar-brand d-none d-sm-block" href="#">
          Menu
        </a>
        <a href="javascript:void(0)" class="navbar-brand d-block d-sm-none">
          <?= $settings->getSiteLogo() ?>
        </a>
        <hr class="mt-0 p-0" />
      </div>

    </div>
  </div>
  <div class="sidebar-content">
    <ul class="list-group list-group-flush">
      <?php if ($_SESSION['admin']): ?>
        <a onclick="$('#spinner').show()" class="text-body" href="<?= $relative_root ?>admin/portal.php">
        <li class="list-group-item">
          Overview
        </li>
        </a>
        <a onclick="$('#spinner').show()" href="<?= $relative_root ?>admin/calendar.php" id="link_calendar" class="text-decoration-none text-body"><li class="list-group-item">Calendar</li></a>
        <a class="text-body" id="btn_reports" href="javascript:void(0)">
          <li class="list-group-item">
            Reports
            <span class="float-right">
              <i class="fa fa-chevron-down" aria-hidden="true"></i>
            </span>
          </li>
        </a>
        <div id="reports_group_wrapper" class="">
          <ul class="list-group list-group-flush" id="reports_group">
          <a onclick="$('#spinner').show()" href="<?= $relative_root ?>admin/reports.php?daily=true" name="daily" class="text-decoration-none text-body align-self-end" style="width:90%;"><li class="list-group-item <?= $daily ? 'active' : '' ?>">Single Day</li></a>
            <a onclick="$('#spinner').show()" href="<?= $relative_root ?>admin/reports.php?weekly=true" name="weekly" class="text-decoration-none text-body align-self-end" style="width:90%;"><li class="list-group-item <?= $weekly ? 'active' : '' ?>">Multiple Days</li></a>
            <a onclick="$('#spinner').show()" href="<?= $relative_root ?>admin/reports.php?attendance_report=true" name="attendance_report" class="text-decoration-none text-body align-self-end" style="width:90%;"><li class="list-group-item <?= $attendance ? 'active' : '' ?>">Attendance</li></a>
            <a onclick="$('#spinner').show()" href="<?= $relative_root ?>admin/reports.php?guest_report=true" name="guest" class="text-decoration-none text-body align-self-end" style="width:90%;"><li class="list-group-item <?= $guest ? 'active' : '' ?>">Guests</li></a>
            <a onclick="$('#spinner').show()" href="<?= $relative_root ?>admin/reports.php?student_report=true" name="student" class="text-decoration-none text-body align-self-end" style="width:90%;"><li class="list-group-item <?= $student ? 'active' : '' ?>">Students</li></a>
            <a onclick="$('#spinner').show()" href="<?= $relative_root ?>admin/report-partials/employee_biweekly.php" class="text-decoration-none text-body align-self-end" style="width:90%;"><li id="employee_biweekly_list_item" class="list-group-item">Employee Biweekly</li></a>
            <a onclick="$('#spinner').show()" href="<?= $relative_root ?>admin/reports.php?employee=true" name="employee" class="text-decoration-none text-body align-self-end" style="width:90%;"><li class="list-group-item <?= $employee ? 'active' : '' ?>">Payroll</li></a>
          </ul>
        </div>
        <a class="text-body" href="javascript:void(0)" id="btn_management">
          <li class="list-group-item">
            User Management
            <span class="float-right">
              <i class="fa fa-chevron-down" aria-hidden="true"></i>
            </span>
          </li>
        </a>
        <div id="management_group_wrapper" class="">
          <ul class="list-group list-group-flush" id="management_group">
            <a onclick="$('#spinner').show()" href="<?= $relative_root ?>admin/users.php" class="text-decoration-none text-body align-self-end" style="width:90%;"><li class="list-group-item" id="users_list_item">Users</li></a>
            <a onclick="$('#spinner').show()" href="<?= $relative_root ?>admin/guests.php" class="text-decoration-none text-body align-self-end" style="width:90%;"><li class="list-group-item" id="guests_list_item">Guests</li></a>
            <a onclick="$('#spinner').show()" class="text-decoration-none text-body align-self-end not-allowed" style="width:90%;"><li class="list-group-item disabled" id="register_list_item">Register User</li></a>
          </ul>
        </div>
        <a onclick="showSpinner()" class="text-body" href="<?= $relative_root ?>admin/settings.php">
        <li class="list-group-item">
          Settings
        </li>
        </a>
        <a onclick="showSpinner()" class="text-danger" href="<?= $relative_root ?>admin/logout.php">
        <li class="list-group-item">
          Logout
        </li>
        </a>
      <?php else: ?>
        <a href="<?= $relative_root ?>index.php">
        <li class="list-group-item">
          Home
        </li>
        <li class="list-group-item d-block d-sm-none" id="login_guest_link_sidebar">
          Guest Sign-in
        </li>
        </a>
        <a class="" href="<?= $relative_root ?>building_report.php">
        <li class="list-group-item">
          Attendance Report
        </li>
        </a>
        <a class="" href="<?= $relative_root ?>admin/login.php">
          <li class="list-group-item">
            Admin
          </li>
        </a>
      <?php endif ?>
    </ul>
  </div>
</div>
<script>
$('#login_guest_link_sidebar').click(function(event) {
  event.preventDefault()
  $('#login_guest_modal').modal('show');
})
</script>
