/**
 * Provides JS functionality for user_form.php
 * Author Steph Mireault
 */

$('#offboard-user-button').click(function(event) {
    if (confirm('Are you sure?') === false) {
        event.preventDefault();
    }
});

$('#onboard-user-button').click(function(event) {
    if (confirm('Are you sure?') === false) {
        event.preventDefault();
    }
})

$('#change-pin-button').click(function(event) {
    $('#spinner').show()
    let url = 'partials/change_user_pin_form.php';
    let get = $.get(url, {
        id: $(this).data('id')
    });

    get.done(function(data) {
        $('#spinner').hide()
        $('#user-form-container').html(data);
    });
});

$('#user-form').submit(function(event) {
    $('#spinner').show()
    let username = $.trim($('#username').val());
    let firstName = $.trim($('#first_name').val());
    let lastName = $.trim($('#last_name').val());
    let expectedClockin = $.trim($('#expected_clockin').val());
    let expectedClockout = $.trim($('#expected_clockout').val());
    let checkUsername = $('#check-username').val();
    let userNameErr = "";
    let flag = true;
    const MAX_LENGTH = 30;

    if (username === "") {
        flag = false;
        usernameErr = "Must not be blank.";
        $('#username-err').html(usernameErr);
        $('#username').addClass('is-invalid');
        event.preventDefault();
    } else if (username.length > MAX_LENGTH) {
        flag = false;
        $('#username-err').html('Must not be longer than 30 characters.');
        $('#username').addClass('is-invalid');
        event.preventDefault();
    } else if (checkUsername === 1) {
        flag = false;
        $('#username-err').html('Username already exists.');
        $('#username').addClass('is-invalid');
    }
    
    if (firstName === "") {
        flag = false;
        $('#first-name-err').html('Must not be blank.');
        $('#first_name').addClass('is-invalid');
        event.preventDefault();
    } else if (firstName.length > MAX_LENGTH) {
        flag = false;
        $('#first-name-err').html('Must not be longer than 30 characters.');
        $('#first_name').addClass('is-invalid');
        event.preventDefault();
    }
    
    if (lastName === "") {
        flag = false;
        $('#last-name-err').html('Must not be blank.');
        $('#last_name').addClass('is-invalid');
        event.preventDefault();
    } else if (lastName.length > MAX_LENGTH) {
        flag = false;
        $('#last-name-err').html('Must not be longer than 30 characters.');
        $('#last_name').addClass('is-invalid');
        event.preventDefault();
    } 
    
    if (flag === true) {
        $('.form-control').removeClass('is-invalid');
        $('.form-control').addClass('is-valid');
        event.preventDefault();
        let form = $(this);
        let url = form.attr('action');
        let post = $.post(url, {
            comments:            $('#comments').val(),
            id:                  $('#id').val(),
            overtime:            $('#overtime').val(),
            first_name:          $('#first_name').val(),
            last_name:           $('#last_name').val(),
            username:            $('#username').val(),
            user_type:           $('#user_type').val(),
            user_position:       $('#user_position').val(),
            expected_clockin:    $('#expected_clockin').val(),
            expected_clockout:   $('#expected_clockout').val(),
            expected_work_hours: $('#expected_work_hours').val(),
            payroll_id:          $('#payroll_id').val()
        });

        post.done(function(data) {
            $('#spinner').hide()
            $('.server-message').show();
            let usernameErrorMessage = $($.parseHTML(data)).filter('#server-username-err').html();
            let serverSuccessMessage = $($.parseHTML(data)).filter('#server-success').html();
            let serverErrorMessage = $($.parseHTML(data)).filter('#server-error').html();
            if (usernameErrorMessage != null) {
                $('#username').addClass('is-invalid');
                $('#username-err').html(usernameErrorMessage);
            } else if (serverErrorMessage != null) {
                $('.form-control').addClass('is-invalid');
                $('.server-message').html(serverErrorMessage);
            } else if (serverSuccessMessage != null) {
                $('.form-control').addClass('is-valid');
                $('.server-message').html(serverSuccessMessage);
                setTimeout(() => {
                    $('.server-message').hide();
                    $('.form-control').removeClass('is-valid');
                }, 3000);
            }
        });
    }
    

});

$('#delete-form a').click(function(event) {
    if(confirm('Are you sure?')) {
        $('#delete-form').submit();
    } else {
        event.preventDefault();
    }
});

$('#delete-form').submit(function(event) {
    event.preventDefault();
    $('#spinner').show()
    let form = $(this);
    let url = form.attr('action');
    let post = $.post(url);

    post.done(function(data) {
        $('#spinner').hide()
        $('#guest-container').html('Changes saved! <i class="fa fa-check" aria-hidden="true" style="color:#0eb80b;font-size:20px;"></i>');
        setTimeout(() => {
            $('#guest-container').hide();
        }, 3000);
    });
});

$('#view-attendances-button').click(function(event) {
    $('#spinner').show()
    let url = 'partials/attendance_form.php';
    let get = $.get(url, {
        id: $(this).data('id')
    });

    get.done(function(data) {
        $('#spinner').hide()
        $('#user-form-container').html(data);
    });
});