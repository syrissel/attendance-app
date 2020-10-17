<?php
require_once('../classes/classes.php');
require('authenticate.php');

?>

<html>
<head>
    <title>Calendar</title>
    <link rel="stylesheet" href="../css/calendar/main.min.css">
</head>
<body>
    <?php include('../nav.php') ?>
    <div class="container mt-4">
        <div id="calendar"></div>
        <button type="button" onclick="window.history.back()" class="btn btn-secondary mt-2">Back</button>
    </div>
    <?php include('../footer.php') ?>
</body>
</html>
<script src="../js/main.js"></script>
