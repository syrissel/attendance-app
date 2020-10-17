<?php
require_once('../classes/classes.php');
require('authenticate.php');
session_start();
require_once('../vendor/autoload.php');

use JasonGrimes\Paginator;

$totalItems = count(User::getEmployeesAndStudents());
$itemsPerPage = 12;
$currentPage = 1;

if ($_GET && isset($_GET['page'])) {
  $currentPage = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT);
}

$offSet = ($currentPage - 1) * $itemsPerPage;

$urlPattern = 'users.php?page=(:num)';

$paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);
$paginator->setMaxPagesToShow(3);

$users = User::getEmployeesAndStudents("limit $itemsPerPage", 'active', "offset $offSet");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <style>
        li {
            cursor: pointer;
        }
    </style>
</head>
<body>
<?php include('../nav.php') ?>
<div id="spinner" class="position-fixed w-100 text-center" style="display:none; z-index:10;">
    <div class="row" style="height:20rem;"></div>

    <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>
<div class="container mt-3">

    <h3 class="text-info my-4 ml-1">User Management</h3>

    <!-- jQuery datepicker seems to select the very first text input on the page to determine its width.
         Since the first text input on the page (the search box) is smaller than the text inputs that actually use the datepicker,
         I put this dummy input with the exact dimensions needed to determine its width. -->
    <div id="datepicker_dummy_container" class="container">
        <div id="datepicker_dummy_row" class="row">
            <div class="col-3"></div>
            <div class="col-6"><input type="text" id="datepicker_dummy" class="form-control"></div>
            <div class="col-3"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-3">
        <ul class="list-group">
            <li class="list-group-item">
                <form id="user_search" method="get">
                    <input type="text" class="form-control float-left w-75" name="search" id="search" placeholder="Search users...">
                    <input type="hidden" name="type" id="type" value="active">
                    <button type="submit" class="btn btn-secondary float-left ml-1"><i class="fa fa-search" aria-hidden="true"></i></button>
                </form>
            </li>
            <div id="user_list">
            <?php foreach ($users as $user): ?>
                <li class="list-group-item list_item" data-id="<?= $user->getID() ?>"><?= $user->getFullName() ?></li>
            <?php endforeach ?>
            <li class="list-group-item"><?= $paginator ?></li>
            <script src="../js/users.js"></script>
            </div>
        </ul>
        </div>
        <div class="col-6">
            <div id="confirmation"></div>
            <div id="user-form-container">Select a user or task to get started.</div>
        </div>
        <div class="col-3">
            <ul class="list-group">
                <li class="list-group-item"><b>Tasks</b></li>
                <a class="m-0 p-0 text-decoration-none text-body not-allowed"><li class="list-group-item list_item disabled"><i class="fa fa-user-plus text-secondary" aria-hidden="true"></i> Add User</li></a>
                <a href="inactive_users.php" class="text-decoration-none text-body"><li class="list-group-item" id="inactive-users"><i class="fa fa-user-times text-secondary" aria-hidden="true"></i> Inactive Users</li></a>
                <a href="#" id="edit-staff-positions" class="text-decoration-none text-body"><li class="list-group-item"><i class="fa fa-address-card text-secondary" aria-hidden="true"></i> Edit Staff Positions</li></a>
                <a href="#" id="edit-absent-reasons" class="text-decoration-none text-body"><li class="list-group-item"><i class="fa fa-list-alt text-secondary" aria-hidden="true"></i> Edit Absent Reasons</li></a>
                <a href="bulk_pto.php" id="link_bulk_pto" class="text-decoration-none text-body"><li class="list-group-item"><i class="fa fa-clock-o text-secondary" aria-hidden="true"></i> Bulk PTO</li></a>
                <a href="javascript:void(0)" id="link_users_help" class="text-decoration-none text-body"><li class="list-group-item"><i class="fa fa-info-circle text-secondary" aria-hidden="true"></i> Help</li></a>
            </ul>
        </div>
    </div>
</div>
<?php include('../footer.php') ?>
<?php include('partials/user_management_help.php') ?>
</body>
</html>

<script src="../js/users.js"></script>
