/**
 * Initializes form values based on attendance data saved in DB.
 * Author: Steph Mireault
 */
$(document).ready(function() {

    $('#pto_form_group').hide()
    $('#pto_group').hide()
    $('#partial_day_group').hide()

    // Hide or show form groups depending on the data that is saved in the db.
    if ($('#sticky_reason').data('reason') === '') {
        $('#reason_radio_container').hide()
        $('#reason-group').hide();
    } else {
        let isChecked = false;
        $('#pto_form_group').show()

        if ($('#pto').val() > 0) {
            $('#pto_group').show()
            $('#pto_check').prop('checked', true)
        }

        if ($('#leaving_early').val() != '') {
            $('#leaving_early_check').prop('checked', true)
            $('#partial_day_group').show()
            $('#leaving_early_group').removeClass('d-none')
            isChecked = true
        }

        if ($('#arriving_late').val() != '') {
            $('#arriving_late_check').prop('checked', true)
            $('#partial_day_group').show()
            $('#arriving_late_group').removeClass('d-none')
            isChecked = true
        }

        if ($('#arriving_early').val() != '') {
            $('#arriving_early_check').prop('checked', true)
            $('#partial_day_group').show()
            $('#arriving_early_group').removeClass('d-none')
            isChecked = true
        }

        if ($('#mid_day_break_from').val() != '') {
            $('#unpaid_check').prop('checked', true)
            $('#partial_day_group').show()
            $('#mid_day_break_group').removeClass('d-none')
            isChecked = true
        }

        if (isChecked) {
            $('#partial_day_rad').prop('checked', true)
            $('.attendance-field').removeAttr('disabled')
            $('#partial_day_group').show()
        } else {
            $('#full_day_rad').prop('checked', true)
        }
    }

    $('.absent-feedback').hide();
});