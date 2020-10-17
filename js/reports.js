$('#btn_finalize_payroll').click(function(event) {
    let message = 'Note: the following generated report is based on employees\' current worked hours, expected hours, absent/late info, and paid time off. If any interns or employees have upcoming days without paid time, please add records for those dates now, otherwise the report will not be accurate.'
    if (!confirm(message)) {
        event.preventDefault();
    }
})