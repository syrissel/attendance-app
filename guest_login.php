<?php
session_start();
require_once('classes/classes.php');

// Redirect if old iOS device.
if (isOS931() && $_POST) {
    if (trim(empty($_POST['name']))) {
        header('location: full_guest_form.php?err=true');
    }
}
//include('guest_login_partial.php');
?>
<!doctype html>
<head>
<link rel="stylesheet" type="text/css" href="/vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
</head>
<body>
<div id="login_guest_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Guest Sign-in</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="container mt-3">
        <div class="row">
            <div class="d-none d-sm-block col-md-2"></div>
            <div class="col-12 col-md-8">
                <?php include('guest_login_partial.php') ?>
            </div>
            <div class="d-none d-sm-block col-md-2"></div>
        </div>
        </div>
        </div>
    </div>
</div>
</body>
</html>

<script>
$('#name-help-block').html('Please enter your first and last name.');

$('#guest-login-form').submit(function(event) {
    let name = $('#name').val();
    let organization = $('#organization').val();
    let flag = true;
    let message = "Please try again";
    let firstNameErr = "";
    let lastNameErr = "";

    if (name.trim() === "") {
        flag = false;
        nameErr = "Please enter your first and last name."
    } 

    if (flag === false) {
        if (name.trim() === "") {
            $('#name').addClass('is-invalid');
            $('#name-help-block').hide();
            $('#name-err').html(nameErr);
        } else {
            $('#name').removeClass('is-invalid');
            $('#name').addClass('is-valid');
        }

        event.preventDefault(); 
    }
});
</script>