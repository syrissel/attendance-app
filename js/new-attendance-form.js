/**
 * Common functionality for new attendance forms.
 */

$(document).ready(function() {
    resetAbsentFields();
    $('#reason-group').hide()
})

$('#arriving_late_check').change(function() {
    if ($(this).is(':checked')) {
        $('#clockin').val('')
    } 
})