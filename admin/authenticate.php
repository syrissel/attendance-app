<?php
require_once(__DIR__ . '/../classes/classes.php');
session_start();

$abs_root_path = __DIR__ . '/..';
require($abs_root_path . '/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();
$relative_root = $_ENV['ROOT'];

if ($_SESSION['admin']) {
    $user = $_SESSION['admin'];
    if (!$user->isAdmin()) {
        header("location: {$relative_root}index.php");
    }
} else {
    header("location: {$relative_root}index.php");
}
?>