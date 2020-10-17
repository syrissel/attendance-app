$('#new-user-attendance-form').submit(function(event) {
    
    let clockIn = $.trim($('#clockin').val());
    let flag = true;
    let ptoReg = /^(([0-9]|1[0-3])(\.\d\d?)?|14(\.00?)?)$/;
    let dateReg = /(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/;
    let ptoStr = document.getElementById('pto').value;
    let isPtoDecimal = ptoStr.indexOf('.') != -1;
    let ptoDecimal = ptoStr.substr(ptoStr.indexOf('.') + 1);
    let dateFlag = false;
    let times = [$('#clockin').val(), $('#clockout').val(), $('#morningin').val(), $('#morningout').val(), $('#lunchin').val(), $('#lunchout').val(),
                 $('#afternoonin').val(), $('#afternoonout').val(), $('#arriving_early').val(), $('#arriving_late').val(), $('#leaving_early').val(), 
                 $('#mid_day_break_from').val(), $('#mid_day_break_to').val()];

    times.forEach(function(item, index) {
        if (item.length > 0)
            if (!dateReg.exec(item))
                dateFlag = true;
    })

    if (dateFlag) {
        $('#error_info').html('<span class="text-danger"><b>Error:</b> It looks like one of the dates is in the wrong format. Please make sure all dates are in the format YYYY-MM-DD HH:MM:SS - Example: 2020-09-29 08:30:00</span>')
        event.preventDefault()
    } else if (clockIn === "" && !$('#absent').is(':checked')) {
        flag = false;
        $('#clockin-err').html('Must not be blank.');
        $('#clockin').addClass('is-invalid');
        event.preventDefault();
    } else if ((clockout.length > 0) && (clockIn > clockout)) {
        flag = false;
        $('#clockin-err').html('Clock In time cannot be over Clock Out time.');
        $('#clockin').addClass('is-invalid');
        $('#clockout').addClass('is-invalid');
        event.preventDefault();
    } else if ($('#pto_check').is(':checked') && !ptoReg.exec($('#pto').val())) {
        $('#pto').addClass('is-invalid')
        $('#pto_err').html('Must be either a whole number or rounded to two decimal points.')
        event.preventDefault()
    } else if ((isPtoDecimal && ptoDecimal != '25') && (isPtoDecimal && ptoDecimal != '5') && (isPtoDecimal && ptoDecimal != '75')) {
        $('#pto').addClass('is-invalid')
        $('#pto_err').html('Must only be in quarter hours. Eg. 1.25, 4.5, 6.75')
        event.preventDefault()
    } else if ($('#absent').is(':checked') && $('#reason').val() == '') {
        $('#reason').addClass('is-invalid')
        event.preventDefault()
    } else if ($('#absent').is(':checked') && $('#reason').val() && !$('input[name="reason_radio"]:checked').val()) {
        $('#reason_radio_err').html('Please select either Full Day or Partial Day.')
        event.preventDefault()
    } else {
        $('.form-control').removeClass('is-invalid');
        //$('.form-control').addClass('is-valid');
        $('#spinner').show()
    }
});
