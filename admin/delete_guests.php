<?php
require_once('../classes/classes.php');
require('authenticate.php');
session_start();

User::deleteAllGuests();
