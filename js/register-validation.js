$(document).ready(function() {

    $('#register-form').submit(function(event) {

        let username = $.trim($('#username').val());
        let firstName = $.trim($('#first_name').val());
        let lastName = $.trim($('#last_name').val());
        let password = $.trim($('#password').val());
        let confirmPassword = $.trim($('#confirm_password').val());
        let valid = true;
        console.log('*****');

        if (username === '') {
            $('#username').addClass('is-invalid');
            $('#username-err').html('Please enter a username.');
            valid = false;
        } else if (username.length > 30) {
            $('#username-err').html('Must not be longer than 30 characters.');
            $('#username').addClass('is-invalid');
            valid = false;
        }

        if (firstName === "") {
            $('#first-name-err').html('Please enter first name.');
            $('#first_name').addClass('is-invalid');
            valid = false;
        } else if (firstName.length > 30) {
            $('#first-name-err').html('Must not be longer than 30 characters.');
            $('#first_name').addClass('is-invalid');
            valid = false;
        }
        
        if (lastName === "") {
            $('#last-name-err').html('Please enter last name.');
            $('#last_name').addClass('is-invalid');
            valid = false;
        } else if (lastName.length > 30) {
            $('#last-name-err').html('Must not be longer than 30 characters.');
            $('#last_name').addClass('is-invalid');
            valid = false;
        } 

        if ((password === "") || !((password.length >= 4) && (password.length <= 6))) {
            $('#password').addClass('is-invalid');
            $('#password-err').html('Please enter a pin of 4 to 6 characters.');
            valid = false;
        } else if (!isNumber(password)) {
            $('#password').addClass('is-invalid');
            $('#password-err').html('PIN must be a number.');
            valid = false;
        } else {
            $('#password').removeClass('is-invalid');
            $('#password').addClass('is-valid');
        }
    
        if ((confirmPassword === "") || !((confirmPassword.length >= 4) && (confirmPassword.length <= 6))) {
            $('#confirm_password').addClass('is-invalid');
            $('#confirm-password-err').html('Please enter a pin of 4 to 6 characters.');
            valid = false;
        } else if (!isNumber(confirmPassword)) {
            $('#confirm_password').addClass('is-invalid');
            $('#confirm-password-err').html('PIN must be a number.');
            valid = false;
        } else if (password !== confirmPassword) {
            $('#confirm_password').addClass('is-invalid');
            $('#confirm-password-err').html('PINs do not match');
            valid = false;
        } else {
            $('#confirm_password').removeClass('is-invalid');
            $('#confirm_password').addClass('is-valid');
        }

        if (!valid) {
            event.preventDefault();
        }
    });

    // Returns true if argument is a string of numbers.
    function isNumber(num) {
        let reg = new RegExp('^\\d+$');
        return reg.test(num);
    }
});