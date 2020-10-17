<?php
session_start();
require('vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
// echo $_ENV['FOLDER'];
// echo '<br/>';
// echo dirname(__FILE__)  . $_ENV['FOLDER'];
// $root_path = dirname(__FILE__)  . '/' . $_ENV['FOLDER'] . '/';

// define('ROOT', $root_path);
// echo '<br />Root path: <br /> ' . ROOT;
// $_SESSION['root_path'] = ROOT;
// echo $_SERVER['DOCUMENT_ROOT'];
echo dirname(__FILE__);
echo '<br />';
echo $_SERVER['DOCUMENT_ROOT'];