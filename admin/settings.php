<?php
require_once('../classes/classes.php');
require('authenticate.php');
session_start();

// Get settings row from DB
$settings = Settings::getInstance();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <style>
        .list-group-item {
            cursor: pointer;
        }

        #snackbar {
            visibility: hidden;
            min-width: 250px;
            margin-left: -125px;
            background-color: rgb(29, 13, 13);
            color: #fff;
            text-align: center;
            border-radius: 2px;
            padding: 16px;
            position: fixed;
            z-index: 9999;
            left: 50%;
            bottom: 50%;
            font-size: 17px;
            }

        #snackbar.show {
            visibility: visible;
            -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
            animation: fadein 0.5s, fadeout 0.5s 2.5s;
        }

        @-webkit-keyframes fadein {
            from {bottom: 0; opacity: 0;} 
            to {bottom: 50%; opacity: 1;}
        }

        @keyframes fadein {
            from {bottom: 0; opacity: 0;}
            to {bottom: 50%; opacity: 1;}
        }

        @-webkit-keyframes fadeout {
            from {bottom: 50%; opacity: 1;} 
            to {bottom: 0; opacity: 0;}
        }

        @keyframes fadeout {
            from {bottom: 50%; opacity: 1;}
            to {bottom: 0; opacity: 0;}
        }
    </style>
</head>
<body>
<?php include('../nav.php') ?>
<div id="spinner" class="position-fixed w-100 text-center" style="display:none; z-index:10;">
    <div class="row" style="height:20rem;"></div>

    <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>
    <div class="container-fluid mt-3">
    <div class="row">
    <div class="col-sm-2 col-lg-3">
        <ul class="list-group">
            <li onclick="$('#spinner').show()" class="list-group-item" id="general">General</li>
            <li class="list-group-item not-allowed text-muted" id="backups">Backups</li>
            <li class="list-group-item not-allowed text-muted" id="import">Import</li>
        </ul>
    </div>
    <div class="col-lg-1"></div>
    <div class="col-sm-8 col-lg-4">
        <div id="settings_container"></div>
    </div><!--col-sm-8 col-6-->
    <div class="col-sm-2 col-lg-4"></div>
    </div><!--row-->
    </div><!--container-->
    <div id="snackbar">Import successful!</div>
    <?php include('../footer.php') ?>
</body>
</html>

<script>
<?php if (isset($_GET['import_success'])): ?>
    $('#snackbar').addClass('show');
    setTimeout(function(){ 
        $('#snackbar').removeClass('show');
    }, 3000);
<?php endif ?>

$('#general').click(function(event) {
    let url = 'settings_partials/general_form.php'
    let get = $.get(url)
    get.done(function(data) {
        $('#spinner').hide()
        $('#settings_container').html(data)
        $('.list-group-item').removeClass('active')
        $('#general').addClass('active')
    })
})
</script>