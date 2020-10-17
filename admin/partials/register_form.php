<?php 

?>
<head>
<script src="../../js/register-validation.js"></script>

</head>
<form id="register-form" action="<?= $path ?>" method="post">
    <div class="form-group">
        <label>Username</label>
        <input type="text" id="username" name="username" value="<?= $username ?>" autocomplete="off" class="form-control <?= (!empty($username_err)) ? 'is-invalid' : ''; ?> <?= (!empty($username)) ? 'is-valid' : ''; ?>">
        <span class="text-danger"><?= $username_err ?></span>
        <div id="username-err" class="invalid-feedback"></div>
    </div>  
    <div class="form-group">
        <label>First Name</label>
        <input type="text" id="first_name" name="first_name" value="<?= $first_name ?>" autocomplete="off" class="form-control <?= (!empty($first_name_err)) ? 'is-invalid' : ''; ?> <?= (!empty($first_name)) ? 'is-valid' : ''; ?>">
        <span class="text-danger"><?= $first_name_err ?></span>
        <div id="first-name-err" class="invalid-feedback"></div>
    </div>  
    <div class="form-group">
        <label>Last Name</label>
        <input type="text" id="last_name" name="last_name" value="<?= $last_name ?>" autocomplete="off" class="form-control <?= (!empty($last_name_err)) ? 'is-invalid' : ''; ?> <?= (!empty($last_name)) ? 'is-valid' : ''; ?>">
        <span class="text-danger"><?= $last_name_err ?></span>
        <div id="last-name-err" class="invalid-feedback"></div>
    </div>
    <div class="form-group">
        <label for="staff_position">Staff Position</label>
        <select name="staff_position" class="form-control <?= (!empty($staff_position_err)) ? 'is-invalid' : ''; ?> <?= (!empty($staff_position)) ? 'is-valid' : ''; ?>">
            <?php foreach (UserPosition::getAllPositions() as $position):  ?>
                <option value="<?= $position->getID() ?>"><?= $position->getPosition() ?></option>
            <?php endforeach ?>
        </select>
        <span class="text-danger"><?= $user_type_err ?></span>
        <div id="user-type-err" class="invalid-feedback"></div>
    </div> 
    <?php if (User::getAllUsers() != null): ?>
    <div class="form-group">
        <label>Access Level</label>
        <select name="user_type" class="form-control <?= (!empty($user_type_err)) ? 'is-invalid' : ''; ?> <?= (!empty($user_type)) ? 'is-valid' : ''; ?>">
            <?php foreach ($types as $type):  ?>
                <option value="<?= $type->getID() ?>"><?= $type->getType() ?></option>
            <?php endforeach ?>
        </select>
        <span class="text-danger"><?= $user_type_err ?></span>
        <div id="user-type-err" class="invalid-feedback"></div>
    </div>  
    <?php else: ?>
        <input type="hidden" id="user_type" name="user_type" value="2">
    <?php endif ?>
    <div class="form-group">
        <label>PIN</label>
        <input type="password" id="password" name="password" class="form-control <?= (!empty($password_err)) ? 'is-invalid' : ''; ?>">
        <span class="text-danger"><?= $password_err ?></span>
        <div id="password-err" class="invalid-feedback"></div>
    </div>
    <div class="form-group">
        <label>Confirm PIN</label>
        <input type="password" id="confirm_password" name="confirm_password" class="form-control <?= (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
        <span class="text-danger"><?= $confirm_password_err ?></span>
        <div id="confirm-password-err" class="invalid-feedback"></div>
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Submit">
        <input type="reset" class="btn btn-default" value="Reset">
        <input type="button" value="Back" onclick="window.history.back()" class="btn btn-secondary float-right">
    </div>
</form>