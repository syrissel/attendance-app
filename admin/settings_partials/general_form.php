<?php
require_once('../../classes/classes.php');
require('../authenticate.php');
session_start();

$settings = Settings::getInstance();
?>

<head>
    <script src="../vendor/summernote/summernote-bs4.js"></script>
    <link rel="stylesheet" href="../vendor/summernote/summernote-bs4.min.css">

</head>
<div class="alert alert-warning">
    <strong>Notice:</strong> These features have been disabled for the live demo. There is no server code handling this form.
    It has been left here for demonstration purposes.

</div>
<form id="general_settings_form" action="settings_partials/general_form.php" method="post">
    <fieldset disabled>
    <h5 class="text-info">General</h5>
    <hr class="pb-1 mt-2">
    <div class="form-group">
        <label for="logo_text">Change Logo</label>
        <input type="text" id="logo_text" name="logo_text" class="not-allowed form-control <?= !empty($logo_text_err) ? 'is-invalid' : '' ?>" id="logo_text" value="<?= $settings->getSiteLogo() ?>" required>
        <div id="logo_text_err" class="text-danger"><?= $logo_text_err ?></div>
        <small class="form-text text-muted ml-1">Changes will show after page refresh.</small>
    </div>
    <div class="form-group">
        <label for="clock_out_time">Set Clock-out Time (optional)</label>
        <input min="12:00" max="23:30" step=1800 type="time" name="clock_out_time" id="clock_out_time" class="form-control not-allowed" value="<?= $settings->getClockOutTime() ?>">
        <div id="clock_out_time_err" class="text-danger"></div>
        <small class="form-text text-muted ml-1">On the clock-in select page, only the clock-out option will show after this time.</small>
    </div>
    <h5 class="text-info pt-2">Custom Notice</h5>
    <hr class="pb-1 mt-2">
    <div class="form-group not-allowed">
        <label for="notice_content">Content</label><br>
        <div id="notice_content" name="notice_content" class="not-allowed"><?= $settings->getNoticeContent() ?></div>
        <input type="button" value="Clear" id="notice_content_reset_button" class="btn btn-link my-2">
    </div>
    <div id="preview_container" class="form-group">
        <label for="notice_content_preview">Preview</label>
        <div id="notice_content_preview" class="alert alert-warning"></div>
    </div>
    <div class="form-group">
        <input type="submit" value="Submit" class="btn btn-primary float-left mr-2 not-allowed">
        <div id="confirmation" class="float-left mt-2 d-none">Changes saved! <i class="fa fa-check text-success" aria-hidden="true"></i><br /></div>
    </div>
    </fieldset>
</form>

<script>
$(document).ready(function() {
    $('.alert-warning').css({'color' : ''});
    var options = [
        ['style', ['style']],
        ['font', ['bold', 'underline', 'strikethrough', 'clear']],
        ['fontname', ['fontname']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['link'],
        ['view', ['fullscreen', 'codeview', 'help']],
    ];

    $('#notice_content').summernote({
        placeholder: 'Type notice content here...',
        tabsize: 2,
        height: 200,
        toolbar: options,
        callbacks: {
            onChange: function(contents, $editable) {
                $('#notice_content_preview').html($(this).summernote('code'));
                displayPreview($(this));
            },
            onInit: function() {
                $('#notice_content_preview').html($('#notice_content').summernote('code'));
                displayPreview($(this));
            }
        }
    })

    $('#notice_content').summernote('disable')
    $('#notice_content').addClass('not-allowed')

    function displayPreview(content) {
        if ($(content).summernote('isEmpty')) {
            $('#preview_container').hide();
        } else {
            $('#preview_container').show();
        }
    }  

    $('#notice_content_reset_button').click(function() {
        $('#notice_content').summernote('reset');
    })
    
})


$('#general_settings_form').submit(function(event) {
    event.preventDefault()
    let valid = true;
    let logoText = $.trim($('#logo_text').val());
    let clockOut = $.trim($('#clock_out_time').val());
    let regClockOut = /^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/;


    if (logoText.length < 1 || logoText.length > 25) {
        valid = false;
        $('#logo_text_err').html('Please enter a name up to 25 characters.');
    }

    if (!regClockOut.test(clockOut)) {
        valid = false;
        $('#clock_out_time_err').html('Please enter a valid time.');
    }

    if (valid) {
        let url = 'settings_partials/general_form.php'
        let post = $.post(url, {
            logo_text:      $('#logo_text').val(),
            clock_out_time: $('#clock_out_time').val(),
            notice_content: $('#notice_content').summernote('code')
        })

        post.done(function(data) {
            $('#confirmation').removeClass('d-none')
        })
    }

})
</script>
