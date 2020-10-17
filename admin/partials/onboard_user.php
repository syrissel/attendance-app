<?php
// Relative to onboard_user.php
require_once('../../classes/classes.php');
require('../authenticate.php');
session_start();

if ($_GET && isset($_GET['id'])) {
    $id = trim(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT));
    $user = User::findByID($id);
    $user->onboard();
    header('location: ../inactive_users.php');
} else {
    header('location: ../users.php');
}