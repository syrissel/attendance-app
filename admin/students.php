<?php
require_once('../classes/classes.php');
require('authenticate.php');
require_once('../vendor/autoload.php');

use JasonGrimes\Paginator;

$totalItems = isset($_GET['inactive']) ? count(User::getAllStudents('', '', 'inactive')) : count(User::getAllStudents());
$itemsPerPage = 2;
$currentPage = 1;

if ($_GET && isset($_GET['page'])) {
  $currentPage = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT);
}

$offSet = ($currentPage - 1) * $itemsPerPage;

$urlPattern = isset($_GET['inactive']) ? 'students.php?page=(:num)&inactive=true' : 'students.php?page=(:num)';

$paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);
$paginator->setMaxPagesToShow(3);

$users = isset($_GET['inactive']) ? User::getAllStudents("limit $itemsPerPage", "offset $offSet", "inactive") : User::getAllStudents("limit $itemsPerPage", "offset $offSet");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students</title>
    <style>
        .list_item {
            cursor: pointer;
        }
    </style>
</head>
<body>
<?php include('../nav.php') ?>
<div class="container mt-3">
<h3 class="text-info my-4 ml-1">Student Management</h3>
    <div class="row">
        <div class="col-3">
        <?php if ($users): ?>
        <div id="user_list">
        <ul class="list-group">
            <li class="list-group-item"><b>Users</b></li>
            <?php foreach ($users as $user): ?>
                <li class="list-group-item list_item" data-id="<?= $user->getID() ?>"><?= $user->getFullName() ?></li>
            <?php endforeach ?>
            <li class="list-group-item"><?= $paginator ?></li>
            <script src="../js/users.js"></script>
        </ul>
        </div>
        <?php endif ?>
        </div>
        <div class="col-6">
            <div id="confirmation">
                <?php if (!$users): ?>
                    <h4 class="text-info">There are no students!</h4>
                    <a href="users.php">Back</a>
                <?php endif ?>
            </div>
            <div id="user-form-container"></div>
        </div>
        <div class="col-3">
            <ul class="list-group">
                <li class="list-group-item"><b>Other Tasks</b></li>
                <?php if (isset($_GET['inactive'])): ?>
                    <a href="students.php" class="text-decoration-none text-body"><li class="list-group-item"><i class="fa fa-users" aria-hidden="true"></i> Active Students</li></a>
                <?php else: ?>
                    <a href="students.php?inactive=true" class="text-decoration-none text-body"><li class="list-group-item"><i class="fa fa-users" aria-hidden="true"></i> Inactive Students</li></a>
                <?php endif ?>
                </ul>
        </div>
    </div>
</div>
<?php include('../footer.php') ?>
</body>
</html>

<script src="../js/users.js"></script>
