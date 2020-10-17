<?php
require_once('classes/classes.php');
session_start();

// if ($_POST && isset($_POST['id']) && isset($_POST['password'])) {
//     $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
//     $password = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING));
//     $user = User::findByID($id);

//     print_r($user);

//     if (!($user->verifyPassword($password))) {
//         echo 'Wrong PIN';
//     } else {
//         $message = 'Please enter new PIN';
//      
//     }
// }
$new_pin_err = $old_pin_err = $confirm_pin_err = $sql_err = "";

if ($_GET && isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $user = User::findByID($id);
}

if ($_POST && isset($_POST['old-pin']) && isset($_POST['new-pin']) && isset($_POST['confirm-pin']) && isset($_POST['id'])) {
    $old_pin = trim(filter_input(INPUT_POST, 'old-pin', FILTER_SANITIZE_STRING));
    $new_pin = trim(filter_input(INPUT_POST, 'new-pin', FILTER_SANITIZE_STRING));
    $confirm_pin = trim(filter_input(INPUT_POST, 'confirm-pin', FILTER_SANITIZE_STRING));
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $user = User::findByID($id);

    if (empty($old_pin)) {
        $old_pin_err = 'Please enter old PIN.';
    } else if ((strlen($old_pin) < 4) && (strlen($old_pin) > 6)) {
        $old_pin_err = 'PIN must be 4 to 6 characters.';
    } else if (!ctype_digit($old_pin)) {
        $old_pin_err = 'PIN must be numeric.';
    } else if (!($user->verifyPassword($old_pin))) {
        $old_pin_err = 'Wrong PIN.';
    }

    if (empty($new_pin)) {
        $new_pin_err = 'Please enter new PIN';
    } else if ((strlen($new_pin) < 4) && (strlen($new_pin) > 6)) {
        $new_pin_err = 'PIN must be 4 to 6 characters.';
    } else if (!ctype_digit($new_pin)) {
        $new_pin_err = 'PIN must be numeric.';
    }

    if (empty($confirm_pin)) {
        $confirm_pin_err = 'Please confirm PIN';
    } else if ((strlen($confirm_pin) < 4) && (strlen($confirm_pin) > 6)) {
        $confirm_pin_err = 'PIN must be 4 to 6 characters.';
    } else if (!ctype_digit($confirm_pin)) {
        $confirm_pin_err = 'PIN must be numeric.';
    } else if ($new_pin != $confirm_pin) {
        $confirm_pin_err = 'PINs do not match.';
        $new_pin_err = 'PINs do not match.';
    }

    if (empty($old_pin_err) && empty($new_pin_err) && empty($confirm_pin_err)) {

        try {
            $user->updatePIN($new_pin);
            header("location: clockin_select.php?user=" . $user->getUsername() . "&pwchange=true");
        } catch (UserException $e) {
            $sql_err = $e->getMessage();
        }
    }
}
?>

<body>
<?php include('nav.php') ?>
    <div class="container mt-3">
        <div class="row">
            <div class="col-sm-auto col-md-2 col-lg-2"></div>
            <div class="col-sm-auto col-md-8 col-lg-8">
                <div class="alert alert-warning text-center py-2">Change PIN</div>
                
                <form id="change-pin-form" action="pin_change.php" method="post">
                    <div class="form-group">
                        <label for="old-pin">Old PIN</label>
                        <input type="password" name="old-pin" id="old-pin" class="form-control <?= (!empty($old_pin_err)) ? 'is-invalid' : ''; ?> <?= (!empty($old_pin)) ? 'is-valid' : ''; ?>">
                        <span class="text-danger"><?= $old_pin_err ?></span>
                    </div>
                    <div class="form-group">
                        <label for="new-pin">New PIN</label>
                        <input type="password" name="new-pin" id="new-pin" class="form-control <?= (!empty($new_pin_err)) ? 'is-invalid' : ''; ?> <?= (!empty($new_pin)) ? 'is-valid' : ''; ?>">
                        <span class="text-danger"><?= $new_pin_err ?></span>
                    </div>
                    <div class="form-group">
                        <label for="confirm-pin">Confirm PIN</label>
                        <input type="password" name="confirm-pin" id="confirm-pin" class="form-control <?= (!empty($confirm_pin_err)) ? 'is-invalid' : ''; ?> <?= (!empty($confirm_pin)) ? 'is-valid' : ''; ?>">
                        <span class="text-danger"><?= $confirm_pin_err ?></span>
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="id" id="id" value="<?= $user->getID() ?>">
                        <input type="button" value="Back" class="btn btn-dark" onclick="window.history.back()">
                        <input type="submit" value="Submit" class="btn btn-primary float-right">
                    </div>
                </form>
                <div class="text-danger"><?= $sql_err ?></div>
            </div><!--col-4-->
            <div class="col-sm-auto col-md-2 col-lg-2"></div>
        </div><!--#row-->
    </div>
</body>