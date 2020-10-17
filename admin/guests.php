<?php
require_once('../classes/classes.php');
require('authenticate.php');
require_once('../vendor/autoload.php');

use JasonGrimes\Paginator;

$totalItems = count(User::getAllGuests());
$itemsPerPage = 10;
$currentPage = 1;
$search = '';

if ($_GET && isset($_GET['search'])) {
    $search = trim(filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING));
}

if ($_GET && isset($_GET['page'])) {
  $currentPage = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT);
}

if ($_POST && isset($_POST['guest'])) {
    $guests_to_delete = filter_input(INPUT_POST, 'guest', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY);

    foreach ($guests_to_delete as $id) {
        $guest = User::findByID($id);
        $guest->deleteGuest();
    }
}

$offSet = ($currentPage - 1) * $itemsPerPage;

$urlPattern = empty($search) ? 'guests.php?page=(:num)' : 'guests.php?page=(:num)&search='. $search;

$paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);

$guests = User::getAllGuests("limit $itemsPerPage", "offset $offSet", $search);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guests</title>
</head>
<body>
<?php include('../nav.php') ?>
<div class="container">
    <div class="w-100 mt-4">
        <form action="guests.php" method="get" class="w-50">
            <div class="row">
                <div class="col pr-0">
                    <div class="form-group">
                        <input type="text" name="search" id="search" class="form-control" placeholder="Search by date">
                    </div>
                </div>
                <div class="col pl-1">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php if (count($guests) < 1): ?>
    <div class="alert alert-primary" role="alert">
        No records found. <a href="guests.php">Refresh page</a>
    </div>
    <?php else: ?>
    <u><h4 class="py-3 font-italic align-self-center">
        Guest Activity
    </h4></u>
    <form action="guests.php?page=<?= $currentPage ?>" method="post" id="guests_form">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Date of visit</th>
                    <th>Sign-in</th>
                    <th>Sign-out</th>
                    <th>Duration</th>
                    <th>Organization</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($guests as $guest): ?>
                <?php
                    $date = new DateTime($guest->getCreatedAt()); 
                    $attendance = Attendance::createWithDate($guest, $date); 
                ?>
                    <tr>
                        <td><input type="checkbox" name="guest[]" id="<?= $guest->getUsername() ?>" value="<?= $guest->getID() ?>"> <label for="<?= $guest->getUsername() ?>"><?= $guest->getFullName() ?></label></td>
                        <td><?= date('M j, Y', strtotime($guest->getCreatedAt())) ?></td>
                        <td><?= formatPunchInTime($attendance->getClockIn()) ?></td>
                        <td><?= formatPunchInTime($attendance->getClockOut()) ?></td>
                        <td><?= getSemanticHours(getTimeDiffInMinutes($attendance->getClockIn(), $attendance->getClockOut(), false)) ?></td>
                        <td><?= $guest->getOrganization() ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn-danger">Delete Selected</button>
    </form>
    <?php endif ?>
    <div class="d-flex justify-content-center">
        <?= $paginator ?>
    </div>
</div>
<?php include('../footer.php') ?>
</body>
</html>
<script src="../js/jquery.daterangepicker.min.js"></script>
<script>
$(document).ready(function () {
  $('.pagination li').addClass('page-item')
  $('.pagination li a').addClass('page-link')

  $('#search').dateRangePicker({
	autoClose: true,
	showShortcuts: false
  })

  $('#guests_form').submit(function (event) {
    if (!confirm('Are you sure?'))
      event.preventDefault()
  })
})
</script>
