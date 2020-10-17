<?php
require_once('../classes/classes.php');
session_start();

$username_err = $password_err = '';
$username = $password = '';

if (isset($_GET['back'])) {
    header('location: ../index.php');
}

if ($_POST && isset($_POST['username']) && isset($_POST['password'])) {

    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
    $password = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING));

    if (empty($username)) {
        $username_err = 'Please select a username';
    }

    if (empty($password)) {
        $password_err = 'Please enter your PIN';
    }

    if (empty($username_err) && empty($password_err)) {
        if ($user = User::findByUsername($username)) {
            if ($user->verifyPassword($password)) {
                if ($user->isAdmin()) {
                    $_SESSION['admin'] = $user;
                    header('location: portal.php');
                } else {
                    echo "<script>alert('You are not an admin!')</script>";
                }
            } else {
                $password_err = 'Incorrect PIN';
            }
        } else {
            $username_err = 'Unable to find this user';
        }
    }
}
?>

<!DOCTYPE html>
<head>
    <link rel="stylesheet" href="../css/pinpad.css">
    <script src="../js/pinpad.js"></script>
</head>
<body>
    <?php include('../nav.php') ?>
    <div class="container mt-3">
    <div class="row">
    <div class="col-sm-auto col-md-2 col-lg-2 d-lg-none"></div>
    <div class="col-sm-auto col-md-8 col-lg-8 d-lg-none">
        <form action="login.php" method="post">
            <div class="form-group">
                <label for="username">Select Name</label>
                <select class="form-control" name="username" id="username">
                    <?php foreach (User::getAdmins() as $user): ?>
                        <option value="<?= $user->getUsername() ?>"><?= $user->getFullName() ?></option>
                    <?php endforeach ?>
                </select>
                <span class="text-danger"><?= $username_err ?></span>
            </div><!--form-group-->
            <div class="form-group">    
                <div class="form-group w-100">
                    <div class="btn-group-vertical w-100">
                    <div class="btn-group">
                        <input type="password" name="password" class="text-center form-control-lg mb-2 w-100" id="code" style="border:2px solid black;font-size:1.5rem !important;">
                        
                    </div>
                    <span class="text-danger"><h5><?= $password_err ?></h5></span>
                    <div class="btn-group">
                        <button id="pin1" type="button" class="btn btn-outline-secondary pin_button"><div>1</div></button>
                        <button id="pin2" type="button" class="btn btn-outline-secondary pin_button"><div>2</div></button>
                        <button id="pin3" type="button" class="btn btn-outline-secondary pin_button"><div>3</div></button>
                    </div>
                    <div class="btn-group">
                        <button id="pin4" type="button" class="btn btn-outline-secondary pin_button"><div>4</div></button>
                        <button id="pin5" type="button" class="btn btn-outline-secondary pin_button"><div>5</div></button>
                        <button id="pin6" type="button" class="btn btn-outline-secondary pin_button"><div>6</div></button>
                    </div>
                    <div class="btn-group">
                        <button id="pin7" type="button" class="btn btn-outline-secondary pin_button"><div>7</div></button>
                        <button id="pin8" type="button" class="btn btn-outline-secondary pin_button"><div>8</div></button>
                        <button id="pin9" type="button" class="btn btn-outline-secondary pin_button"><div>9</div></button>
                    </div>
                    <div class="btn-group">
                        <button id="del" type="button" class="btn btn-outline-secondary pin_button px-2"><div><i class="fa fa-window-close" aria-hidden="true"></i></div></button>
                        <button id="pin0" type="button" class="btn btn-outline-secondary pin_button"><div>0</div></button>
                        <button id="go" type="submit" class="btn btn-primary py-2" >Go</button>
                    </div>
                    </div>
                </div>
                <p><a class="btn btn-dark" href="login.php?back=true">Back</a>
            </div>
        </form>
        </div><!--col-6-->
        <div class="col-sm-auto col-md-2 col-lg-2 d-lg-none"></div>
        <div class="col-3 d-none d-lg-block"></div>
        <div class="col-6 d-none d-lg-block">
            <h4 class="text-primary">Admin Login</h4>
            <hr class="pt-0 mt-0">
            <form action="login.php" method="post">
                <div class="form-group">
                    <label for="username">Select Name</label>
                    <select class="form-control" name="username" id="username">
                        <?php foreach (User::getAdmins() as $user): ?>
                            <option value="<?= $user->getUsername() ?>"><?= $user->getFullName() ?></option>
                        <?php endforeach ?>
                    </select>
                    <span class="text-danger"><?= $username_err ?></span>
                </div><!--form-group-->
                <div class="form-group">
                    <label for="passowrd">PIN</label>
                    <input type="password" name="password" class="form-control">
                    <span class="text-danger"><?= $password_err ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" value="Submit" class="btn btn-primary">
                </div>
            </form>
        </div>
        <div class="col-3 d-none d-lg-block"></div>
        </div><!--#row-->
    </div>
    <?php include('../footer.php') ?>
</body>
</html>