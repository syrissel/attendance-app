<?php
require_once('../../classes/classes.php');
require('../authenticate.php');
require_once('../../vendor/autoload.php');

if ($_GET && isset($_GET['date_range_error'])) {
    $date_range_err = trim(filter_input(INPUT_GET, 'date_range_error', FILTER_SANITIZE_STRING));
}

if ($_GET && isset($_GET['user_error'])) {
    $user_err = trim(filter_input(INPUT_GET, 'user_error', FILTER_SANITIZE_STRING));
}

use JasonGrimes\Paginator;

$totalItems = count(User::getAllEmployees());
$itemsPerPage = 15;
$currentPage = 1;

if ($_GET && isset($_GET['page'])) {
  $currentPage = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT);
}

$offSet = ($currentPage - 1) * $itemsPerPage;

$urlPattern = 'employee_biweekly.php?page=(:num)';

$paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);
$paginator->setMaxPagesToShow(3);

$users = User::getAllEmployees("limit $itemsPerPage", 'first_name', "offset $offSet");

?>
<!doctype html>
<html>
    <head>
        <link rel="stylesheet" href="../../css/pinpad.css">
    </head>
    <body>
        <?php include('../../nav.php') ?>
        <div id="spinner" class="position-fixed w-100 text-center" style="display:none; z-index:10;">
            <div class="row" style="height:20rem;"></div>

            <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <div class="container mt-4">
            <div class="row">
                <div class="col-sm-2 col-md-3 col-lg-3"></div>
                <div class="col-sm-8 col-md-6 col-lg-6 bg-light border rounded py-2 py-3 shadow form-container">
                    <h4 class="report-title">Employee Biweekly Report</h4>
                    <hr class="mt-0">
                    <div class="w-100 d-flex justify-content-center">
                        <form class="report-form" id="employee-form" action="../employee_attendance_report.php" method="get" class="mx-auto">
                        <div class="form-group">
                                <label for="">Select a two week period</label>
                                <div id="datepicker_container">
                                    <input type="hidden" id="employee_biweekly" class="form-control" name="date_range">
                                </div>
                                <span class="text-danger"><?= $date_range_err ?></span>
                            </div>
                            <div class="form-group">
                                <label for="">Select a user</label>
                                <div style="width:100%;height:400px;overflow-y:scroll;" class="border border-dark rounded p-2">
                                    <?php foreach ($users as $user): ?>
                                        <div class="form-check">
                                            <input type="radio" name="id" value="<?= $user->getID() ?>" id="<?= $user->getID() ?>">
                                            <label for="<?= $user->getID() ?>"><?= $user->getFullName() ?></label>
                                        </div>
                                    <?php endforeach ?>
                                    <div class="custom-paginator d-flex w-100 justify-content-center">
                                      <?= $paginator ?>
                                    </div>
                                </div>
                                <span class="text-danger"><?= $user_err ?></span>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div><!--col-md-6-->
                <div class="col-sm-2 col-md-3 col-lg-3"></div>
            </div><!--row-->
        </div><!--container-->
        <?php include('../../footer.php') ?>
        <script src="../../js/employee-biweekly.js"></script>
        <script src="../../js/paginator.js"></script>
    </body>
</html>
