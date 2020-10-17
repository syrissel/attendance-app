/**
 * Provides JS functionality for user_attendance_form.php and new_attendance_form.php.
 * Each page's form submission is still handled on their own page.
 * Author: Steph Mireault
 */

function resetAbsentFields() {
    $('#pto_form_group').hide()
    $('#pto_group').hide()
    $('#unpaid_form_group').hide()
    $('#unpaid_group').hide()
    $('#reason_radio_container').hide()
    $('#full_day_rad').prop('checked', false)
    $('#partial_day_rad').prop('checked', false)
    $('#pto_check').prop('checked', false)
    $('#unpaid_check').prop('checked', false)
    $('#pto').val(0)
    $('.attendance-field').removeAttr('placeholder')
    $('.attendance-field').removeAttr('disabled')
    $('.absent-feedback').hide()
    $('#clockin').val($('#sticky_clockin').val());
    $('#clockout').val($('#sticky_clockout').val());
    $('.partial-field').val('')
    $('.partial-check').prop('checked', false)
    $('#partial_day_group').hide()
    $('.partial-form-group').addClass('d-none')
}

$('#reason').on('change', function (event) {
    if ($(this).val() != '') {
        $('#reason_radio_container').show()
        $(this).removeClass('is-invalid')
    } else {
        $('#reason_radio_container').hide()
        resetAbsentFields()
    }
})

$('#absent').change(function() {
    if ($('#absent').is(':checked')) {
        $('#reason-group').show();

    } else {
        resetAbsentFields()
        $('#reason-group').hide();
        $('#reason').val('');
    }
});

$('#pto_check').change(function() {
    if ($(this).is(':checked')) {
        $('#pto_group').show()
    } else {
        $('#pto_group').hide()
        $('#pto').val(0)
    }
})

$('#unpaid_check').change(function() {
    if ($(this).is(':checked')) {
        $('#mid_day_break_group').removeClass('d-none')
    } else {
        $('#mid_day_break_group').addClass('d-none')
        $('#mid_day_break_from').val('')
        $('#mid_day_break_to').val('')
    }
})

$('#arriving_late_check').change(function() {
    if ($(this).is(':checked')) {
        $('#arriving_late_group').removeClass('d-none')
    } else {
        $('#arriving_late_group').addClass('d-none')
        $('#arriving_late').val('')
    }
})

$('#leaving_early_check').change(function() {
    if ($(this).is(':checked')) {
        $('#leaving_early_group').removeClass('d-none')
    } else {
        $('#leaving_early_group').addClass('d-none')
        $('#leaving_early').val('')
    }
})

$('#arriving_early_check').change(function() {
    if ($(this).is(':checked')) {
        $('#arriving_early_group').removeClass('d-none')
    } else {
        $('#arriving_early_group').addClass('d-none')
        $('#arriving_early').val('')
    }
})

$('input[name="reason_radio"]').change(function() {

    $('#pto_form_group').show()

    if ($('#full_day_rad').is(':checked')) {
        $('.attendance-field').attr('disabled', 'disabled');
        $('#absent_length_to').val('')
        $('#absent_length_from').val('')
        $('#leaving_early').val('')
        $('#arriving_late').val('')
        $('#mid_day_break_to').val('')
        $('#mid_day_break_from').val('')
        $('#unpaid_check').prop('checked', false)
        $('#arriving_late_check').prop('checked', false)
        $('#leaving_early_check').prop('checked', false)
        $('.partial-options-group').addClass('d-none')
        // Set these to empty for full day because we don't want an error showing on payroll that this record has a missing in/out.
        $('#clockin').val('');
        $('#clockout').val('');
        $('.absent-feedback').show();
        $('#partial_day_group').hide()

    } else {
        $('.attendance-field').removeAttr('disabled');
        $('.attendance-field').removeAttr('placeholder');
        $('#clockin').val($('#sticky_clockin').val());
        $('#clockout').val($('#sticky_clockout').val());
        $('.absent-feedback').hide();
        $('#partial_day_group').show()
        $('#partial_fields').show()
    }
})

// new_attendance_form.php
$('#create-attendance-record').click(function(event) {
    $('#spinner').show()
    let url = 'partials/new_attendance_form.php';

    let get = $.get(url, {
        id: $(this).data('id'),
        date: $(this).data('date')
    });

    get.done(function(data) {
        $('#spinner').hide()
        $('#user-form-container').html(data);
    });
});

$('#link_clear_clockin').click(function () {
    $('#clockin').val('')
    $('#clear_confirm').html('Clock-in cleared!')
    setTimeout(() => {
        $('#clear_confirm').html('')
    }, 2000);
})
