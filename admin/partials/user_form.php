<?php
require_once('../../classes/classes.php');
require('../authenticate.php');
session_start();

if ($_GET && isset($_GET['id'])) {
    //$id = filter_input(INPUT_REQUEST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if ($form_user = User::findByID($id)) {
        $selected_position = $form_user->getUserPosition()->getID();
        $selected_type = $form_user->getUserType()->getID();
    }
}

if ($_POST && isset($_POST['id']) && isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['username']) && isset($_POST['user_type']) && isset($_POST['user_position']) && isset($_POST['expected_clockin']) && isset($_POST['expected_clockout']) && isset($_POST['expected_work_hours'])) {
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $first_name = trim(filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING));
    $last_name = trim(filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING));
    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
    $user_type = trim(filter_input(INPUT_POST, 'user_type', FILTER_SANITIZE_NUMBER_INT));
    $user_position = trim(filter_input(INPUT_POST, 'user_position', FILTER_SANITIZE_NUMBER_INT));
    $expected_clockin = trim(filter_input(INPUT_POST, 'expected_clockin', FILTER_SANITIZE_STRING));
    $expected_clockout = trim(filter_input(INPUT_POST, 'expected_clockout', FILTER_SANITIZE_STRING));
    $expected_work_hours = trim(filter_input(INPUT_POST, 'expected_work_hours', FILTER_SANITIZE_NUMBER_INT));
    $payroll_id = trim(filter_input(INPUT_POST, 'payroll_id', FILTER_SANITIZE_STRING));
    $form_user = User::findByID($id);

    // Comments and overtime fields are optional.
    $comments = (isset($_POST['comments'])) ? trim(filter_input(INPUT_POST, 'comments', FILTER_SANITIZE_STRING)) : '';
    $overtime = (isset($_POST['overtime'])) ? trim(filter_input(INPUT_POST, 'overtime', FILTER_SANITIZE_STRING)) : '';

    // Check if the form username exists and not equal to the current user's username.
    if (User::usernameExists($username) && ($username !== $form_user->getUsername())) {
        echo '<span class="text-danger" id="server-username-err">Username has already been taken.</span>';
    } else {
        try {
            $form_user->updateFields($comments, $overtime, $first_name, $last_name, $username, $user_type, $user_position);
            $form_user->updateExpectedClockIn($expected_clockin);
            $form_user->updateExpectedClockOut($expected_clockout);
            $form_user->updateExpectedWorkHours($expected_work_hours);
            $form_user->updatePayrollID($payroll_id);
            echo '<div id="server-success"><h5 class="text-success">Changes saved!</h5></div>';
        } catch (PDOException $e) {
            echo '<div id="server-error"><h5 class="text-danger">' . $e->getMessage() . '</h5></div>';
        }
        
    }
}

// Get get absolute path of current file.
$root_path = trim(__DIR__ . '/../../');
require($root_path . '/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable($root_path);
$dotenv->load();
$relative_root = $_ENV['ROOT'];
?>

<?php if ($form_user): ?>
<h4 class="text-info"><?= $form_user->getFullName() ?> <i style="font-size:20px;" class="fa fa-angle-double-right" aria-hidden="true"></i> Info</h4>
<div class="btn-group float-right ml-2" role="group">
    <button id="btnGroupDrop" type="button" class="btn btn-outline-dark float-right dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      Other
    </button>
    <div class="dropdown-menu" aria-labelledby="btnGroupDrop">
        <a href="#" id="change-pin-button" class="dropdown-item" data-id="<?= $form_user->getID() ?>">Change PIN</a>
        <?php if ($form_user->getStatus() == 'active'): ?>
            <a href="partials/offboard_user.php?id=<?= $form_user->getID() ?>" id="offboard-user-button" class="dropdown-item text-danger">Off-board</a>
        <?php else: ?>
            <a href="partials/onboard_user.php?id=<?= $form_user->getID() ?>" id="onboard-user-button" class="dropdown-item text-primary">Onboard</a>
        <?php endif ?>
    </div>
  </div>
<button id="view-attendances-button" type="button" class="btn btn-outline-dark float-right" data-id="<?= $form_user->getID() ?>">View Attendances</button>
<div class="server-message"></div>
<form id="user-form" action="partials/user_form.php" method="post">
    <h5 class="pt-2 text-info">General</h5>
    <hr class="pb-1 mt-3" />
    <div class="form-group">
        <label for="first_name">First Name</label>
        <input class="form-control" type="text" name="first_name" id="first_name" value="<?= $form_user->getFirstName() ?>">
        <div id="first-name-err" class="invalid-feedback"></div>
    </div>
    <div class="form-group">
        <label for="last_name">Last Name</label>
        <input class="form-control" type="text" name="last_name" id="last_name" value="<?= $form_user->getLastName() ?>">
        <div id="last-name-err" class="invalid-feedback"></div>
    </div>
    <div class="form-group">
        <label for="username">Username</label>
        <input class="form-control" type="text" name="username" id="username" value="<?= $form_user->getUsername() ?>">
        <input type="hidden" name="check-username" id="check-username" value="<?= $username_exists ?>">
        <div id="username-err" class="invalid-feedback"></div>
    </div>
    <div class="form-group">
        <label>Staff Access</label>
        <select id="user_type" name="user_type" class="form-control" value="mother">
            <option value=""></option>
            <?php foreach (UserType::getAllTypes(false) as $type):  ?>
                <option value="<?= $type->getID() ?>" <?= ($type->getID() == $selected_type) ? "selected" : ""  ?>><?= $type->getType() ?></option>
            <?php endforeach ?>
        </select>
    </div>  
    <div class="form-group">
        <label>Staff Position</label>
        <select id="user_position" name="user_position" class="form-control">
            <?php foreach (UserPosition::getAllPositions() as $position):  ?>
                <option value="<?= $position->getID() ?>" <?= ($position->getID() == $selected_position) ? "selected" : ""  ?>><?= $position->getPosition() ?></option>
            <?php endforeach ?>
        </select>
    </div>  
    <h5 class="pt-2 text-info">Payroll Options</h5>
    <hr class="pb-1 mt-2" />
    <div class="form-group">
        <label for="expected_clockin">Expected Clock-in Time</label>
        <input type="time" name="expected_clockin" id="expected_clockin" step=1800 class="form-control" value="<?= $form_user->getExpectedClockIn() ?>">
        <div id="expected-clockin-err" class="invalid-feedback"></div>
        <small class="form-text text-muted">
            User's clock-in time will be set to this if they clock-in before this time. Any clock-in past this time 
            will be rounded to quarter-hours. eg. If the expected clock-in time is 8:30, and the user clocks-in at 8:00, it will round up to 8:30.
            If the user clocks-in at 8:45, it will round up to 9:00, 9:10 will round up to 9:15, and so on.
        </small>
    </div>
    <div class="form-group">
        <label for="expected_clockout">Expected Clock-out Time</label>
        <input type="time" name="expected_clockout" id="expected_clockout" step=1800 class="form-control" value="<?= $form_user->getExpectedClockOut() ?>">
        <div id="expected-clockout-err" class="invalid-feedback"></div>
        <small class="form-text text-muted">
            User's clock-out time will be set to this if they clock-out within 15 minutes, non-inclusive, of this time. eg. If the expected clock-out time is 16:30,
            and the user clocks out at 16:16 or 16:46, it will be set to 16:30.
        </small>
    </div>
    <div class="form-group">
        <label for="expected_work_hours">Expected Biweekly Work Hours</label>
        <input type="number" name="expected_work_hours" id="expected_work_hours" value="<?= $form_user->getExpectedWorkHours() ?>" class="form-control">
        <span id="expected_work_hours_err" class="text-danger"></span>
    </div>
    <div class="form-group">
        <label for="payroll_id">Payroll ID</label>
        <input type="text" name="payroll_id" id="payroll_id" class="form-control" value="<?= $form_user->getPayrollID() ? $form_user->getPayrollID() : '' ?>">
        <span id="payroll_id_err" class="text-danger"></span>
    </div>
    <div class="form-group">
        <input type="hidden" name="id" id="id" value="<?= $form_user->getID() ?>">
        <input class="btn btn-primary float-left mr-2" type="submit" value="Update">
        <div class="server-message float-right"></div>
    </div>
</form>
<?php else: ?>
<h4 class="text-info">Guests</h4>
<div id="guest-container">
<table class="table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Date of visit</th>
            <th>Organization</th>
        </tr>
    </thead>
    <tbody>
    
    <?php foreach(User::getAllGuests() as $guest): ?>
    <tr>
        <td><?= $guest->getFullName() ?></td>
        <td><?= date('M j, Y', strtotime($guest->getCreatedAt())) ?></td>
        <td><?= $guest->getOrganization() ?></td>
    </tr>
    <?php endforeach ?>
    </tbody>
</table>
<form id="delete-form" action="delete_guests.php" method="post">
    <div class="form-group">
        <a class="btn btn-danger text-white">Delete All Guests</a>
    </div>
</form>
</div>
<?php endif ?>

<script src="<?= $relative_root ?>js/nav-links.js"></script>
<script src="<?= $relative_root ?>js/user-form.js"></script>
