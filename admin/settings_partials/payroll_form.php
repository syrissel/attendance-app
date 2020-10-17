<?php
require_once('../../classes/classes.php');
require('../authenticate.php');
require_once '../../vendor/autoload.php';

$phone_err = $toll_free_err = '';
$settings = Settings::getInstance();

if ($_POST && isset($_POST['phone']) && isset($_POST['toll_free'])) {
    $phone = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING));
    $toll_free = trim(filter_input(INPUT_POST, 'toll_free', FILTER_SANITIZE_STRING));
    $phone_regex = "/\D*([2-9]\d{2})(\D*)([2-9]\d{2})(\D*)(\d{4})\D*/";
    $replacement = ' $1-$3-$5';
    
    if (preg_match($phone_regex, $phone)) {
        $replaced_phone = preg_replace($phone_regex, $replacement, $phone);
    } else {
        $phone_err = 'Please enter a valid phone number. Eg. 204-123-4567, 1-888-123-4567';
    }

    if (preg_match($phone_regex, $toll_free)) {
        $replaced_toll_free = preg_replace($phone_regex, $replacement, $toll_free);
    } else {
        $toll_free_err = 'Please enter a valid phone number. Eg. 204-123-4567, 1-888-123-4567';
    }

    if (empty($phone_err) && empty($toll_free_err)) {

        // Trim values again to ensure no trailing white spaces.
        $settings->updatePhone(trim($replaced_phone));
        $settings->updateTollFree(trim($toll_free));
    }
}
?>
<div class="alert alert-danger d-none" id="general_err"></div>
<form id="payroll_settings_form" action="settings_partials/payroll_form.php" method="post">
    <h5 class="text-info">Payroll</h5>
    <hr class="pb-1 mt-2">
    <div class="form-group">
        <label for="phone">Service Team Primary Phone</label>
        <input type="text" name="phone" id="phone" class="form-control" value="<?= $settings->getServiceTeamPhone() ?>" autocomplete="off">
        <small class="text-muted">Must be a 9 or 10 digit North American phone number.</small>
        <div id="phone_err" class="text-danger"><?= $phone_err ?></div>
    </div>
    <div class="form-group">
        <label for="toll_free">Service Team Toll Free</label>
        <input type="text" name="toll_free" id="toll_free" class="form-control" value="<?= $settings->getServiceTeamTollFree() ?>" autocomplete="off">
        <small class="text-muted">Must be a 9 or 10 digit North American phone number.</small>
        <div id="toll_free_err" class="text-danger"><?= $toll_free_err ?></div>
    </div>
    <div class="form-group">
        <input type="submit" value="Submit" class="btn btn-primary float-left mr-2">
        <div id="confirmation" class="float-left mt-2 d-none">Changes saved! <i class="fa fa-check text-success" aria-hidden="true"></i><br /></div>
    </div>
</form>

<script>
$(document).ready(function () {
    $('#payroll_settings_form').submit(function (event) {
        event.preventDefault()
        $('#spinner').show()
        let phone_err = toll_free_err = ''
        let regex = /\D*([2-9]\d{2})(\D*)([2-9]\d{2})(\D*)(\d{4})\D*/
        let replacement = ' $1-$3-$5'
        let phone = $('#phone').val()
        let tollFree = $('#toll_free').val()
        let valid = regex.test(phone) && regex.test(tollFree)
        let replacedPhone = ''
        let replacedTollFree = ''

        if (regex.test(phone)) {
            $('#phone_err').html('')
            replacedPhone = phone.replace(regex, replacement)
        } else {
            phone_err = 'Please enter a valid phone number. Eg. 204-123-4567, 1-888-123-4567';
            $('#phone_err').html(phone_err)
        }

        if (regex.test(tollFree)) {
            $('#toll_free_err').html('')
            replacedTollFree = tollFree.replace(regex, replacement)
        } else {
            toll_free_err = 'Please enter a valid phone number. Eg. 204-123-4567, 1-888-123-4567';
            $('#toll_free_err').html(toll_free_err)
        }

        if (valid) {
            console.log(replacedPhone)
            console.log(replacedTollFree)
            let url = $(this).attr('action')
            let post = $.post(url, {
                toll_free: replacedTollFree,
                phone:     replacedPhone
            })

            post.done(function (data) {
                $('#spinner').hide()
                $('#confirmation').removeClass('d-none');
                setTimeout(() => {
                    $('#confirmation').addClass('d-none');
                }, 2000);
            })
        } else {
            $('#general_err').removeClass('d-none').html('Something went wrong processing data. Please try again.')
        }
    })
})
</script>
