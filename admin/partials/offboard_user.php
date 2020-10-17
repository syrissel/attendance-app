<?php
// Relative to offboard_user.php
require_once('../../classes/classes.php');
require('../authenticate.php');
session_start();

if ($_GET && isset($_GET['id'])) {
    $id = trim(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT));
    $user = User::findByID($id);
    $user->offboard();
    header('location: ../users.php');
} else {
    header('location: ../portal.php');
}
