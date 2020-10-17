<?php
require_once('../classes/classes.php');
require('authenticate.php');
require('../vendor/autoload.php');

use JasonGrimes\Paginator;

$totalItems = count(User::getInactiveEmployees());
$itemsPerPage = 12;
$currentPage = 1;

if ($_GET && isset($_GET['page'])) {
  $currentPage = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT);
}

$offSet = ($currentPage - 1) * $itemsPerPage;

$urlPattern = 'inactive_users.php?page=(:num)';

$paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);
$paginator->setMaxPagesToShow(3);

$users = User::getInactiveEmployees("limit $itemsPerPage", "offset $offSet");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inactive Employees</title>
    <style>
        li {
            cursor: pointer;
        }
    </style>
</head>
<body>
<?php include('../nav.php') ?>
<div class="container mt-3">
<h3 class="text-info my-4 ml-1">Inactive Users</h3>
    <div class="row">
        <div class="col-3">
        <?php if ($users): ?>
            <ul class="list-group">
                <li class="list-group-item">
                    <form id="user_search" method="get">
                        <input type="text" class="form-control float-left w-75" name="search" id="search" placeholder="Search users...">
                        <input type="hidden" name="type" id="type" value="inactive">
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
        <?php endif ?>
        </div>
        <div class="col-6">
            <div id="confirmation">
                <?php if (!$users): ?>
                    <h4 class="text-info">There are no inactive users!</h4>
                    <a href="users.php">Back</a>
                <?php endif ?>
            </div>
            <div id="user-form-container"></div>
        </div>
        <div class="col-3">
            <ul class="list-group">
                <li class="list-group-item"><b>Tasks</b></li>
                <a href="users.php" class="text-decoration-none text-body"><li class="list-group-item"><i class="fa fa-users text-secondary" aria-hidden="true"></i> Active Users</li></a>
            </ul>
        </div>
    </div>
</div>
<?php include('../footer.php') ?>
</body>
</html>
