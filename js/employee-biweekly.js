$(document).ready(function () {
    $('#employee_biweekly').dateRangePicker({
        batchMode: 'week-range',
        showShortcuts: false,
        inline:true,
        customTopBar: 'Please select a two week range',
        container: '#datepicker_container',
        alwaysOpen:true,
        minDays: 8,
        maxDays: 15
    });

    $('.date-picker-wrapper .footer').hide();
    $('.default-top').hide();
    $('.custom-top').hide();

    $('.employee-form').submit(function() {
        $('#spinner').show()
    })
})