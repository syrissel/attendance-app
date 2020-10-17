/* jquery datepicker form initialization.
** Created: July 20 2020
** Author:  Steph Mireault
*/


$('#clockin').dateRangePicker({
	separator : ' ~ ',
    singleDate: true,
    showShortcuts: false,
    singleMonth: true,
	format: 'YYYY-MM-DD HH:mm:ss',
	autoClose: false,
	time: {
		enabled: true
    },
    beforeShowDay: function(t)
	{
        // Repeating this for every datepicker object otherwise I get an error.
        // There's probably a better way to do this.
        // Yes, this is very bad :(
        let date = $('.datepicker-time').val();

        let today = new Date(date);
        let months = ["January", "February", "March", "April", "May", "June", "July",
                    "August", "September", "October", "November", "December"];
        message = 'Please select ' + months[today.getMonth()] + ' ' + today.getDate();

        // Only the selected date is editable.
        let date1 = t.getYear() + '-' + t.getMonth() + '-' + t.getDate();
        let date2 = today.getYear() + '-' + today.getMonth() + '-' + today.getDate();
		var valid = (date1 == date2);
		var _class = '';
		var _tooltip = valid ? '' : message;
		return [valid,_class,_tooltip];
	}
});

$('#clockout').dateRangePicker({
	separator : ' ~ ',
    singleDate: true,
    showShortcuts: false,
    singleMonth: true,
	format: 'YYYY-MM-DD HH:mm:ss',
	autoClose: false,
	time: {
		enabled: true
    },
    beforeShowDay: function(t)
	{
        // Repeating this for every datepicker object otherwise I get an error.
        // There's probably a better way to do this.
        // Yes, this is very bad :(
        let date = $('.datepicker-time').val();

        let today = new Date(date);
        let months = ["January", "February", "March", "April", "May", "June", "July",
                    "August", "September", "October", "November", "December"];
        message = 'Please select ' + months[today.getMonth()] + ' ' + today.getDate();

        // Only the selected date is editable.
        let date1 = t.getYear() + '-' + t.getMonth() + '-' + t.getDate();
        let date2 = today.getYear() + '-' + today.getMonth() + '-' + today.getDate();
		var valid = (date1 == date2);
		var _class = '';
		var _tooltip = valid ? '' : message;
		return [valid,_class,_tooltip];
	}
});

$('#morningin').dateRangePicker({
	separator : ' ~ ',
    singleDate: true,
    showShortcuts: false,
    singleMonth: true,
	format: 'YYYY-MM-DD HH:mm:ss',
	autoClose: false,
	time: {
		enabled: true
    },
    beforeShowDay: function(t)
	{
        // Repeating this for every datepicker object otherwise I get an error.
        // There's probably a better way to do this.
        // Yes, this is very bad :(
        let date = $('.datepicker-time').val();

        let today = new Date(date);
        let months = ["January", "February", "March", "April", "May", "June", "July",
                    "August", "September", "October", "November", "December"];
        message = 'Please select ' + months[today.getMonth()] + ' ' + today.getDate();

        // Only the selected date is editable.
        let date1 = t.getYear() + '-' + t.getMonth() + '-' + t.getDate();
        let date2 = today.getYear() + '-' + today.getMonth() + '-' + today.getDate();
		var valid = (date1 == date2);
		var _class = '';
		var _tooltip = valid ? '' : message;
		return [valid,_class,_tooltip];
	}
});

$('#morningout').dateRangePicker({
	separator : ' ~ ',
    singleDate: true,
    showShortcuts: false,
    singleMonth: true,
	format: 'YYYY-MM-DD HH:mm:ss',
	autoClose: false,
	time: {
		enabled: true
    },
    beforeShowDay: function(t)
	{
        // Repeating this for every datepicker object otherwise I get an error.
        // There's probably a better way to do this.
        // Yes, this is very bad :(
        let date = $('.datepicker-time').val();

        let today = new Date(date);
        let months = ["January", "February", "March", "April", "May", "June", "July",
                    "August", "September", "October", "November", "December"];
        message = 'Please select ' + months[today.getMonth()] + ' ' + today.getDate();

        // Only the selected date is editable.
        let date1 = t.getYear() + '-' + t.getMonth() + '-' + t.getDate();
        let date2 = today.getYear() + '-' + today.getMonth() + '-' + today.getDate();
		var valid = (date1 == date2);
		var _class = '';
		var _tooltip = valid ? '' : message;
		return [valid,_class,_tooltip];
	}
});

$('#lunchin').dateRangePicker({
	separator : ' ~ ',
    singleDate: true,
    showShortcuts: false,
    singleMonth: true,
	format: 'YYYY-MM-DD HH:mm:ss',
	autoClose: false,
	time: {
		enabled: true
    },
    beforeShowDay: function(t)
	{
        // Repeating this for every datepicker object otherwise I get an error.
        // There's probably a better way to do this.
        // Yes, this is very bad :(
        let date = $('.datepicker-time').val();

        let today = new Date(date);
        let months = ["January", "February", "March", "April", "May", "June", "July",
                    "August", "September", "October", "November", "December"];
        message = 'Please select ' + months[today.getMonth()] + ' ' + today.getDate();

        // Only the selected date is editable.
        let date1 = t.getYear() + '-' + t.getMonth() + '-' + t.getDate();
        let date2 = today.getYear() + '-' + today.getMonth() + '-' + today.getDate();
		var valid = (date1 == date2);
		var _class = '';
		var _tooltip = valid ? '' : message;
		return [valid,_class,_tooltip];
	}
});

$('#lunchout').dateRangePicker({
	separator : ' ~ ',
    singleDate: true,
    showShortcuts: false,
    singleMonth: true,
	format: 'YYYY-MM-DD HH:mm:ss',
	autoClose: false,
	time: {
		enabled: true
    },
    beforeShowDay: function(t)
	{
        // Repeating this for every datepicker object otherwise I get an error.
        // There's probably a better way to do this.
        // Yes, this is very bad :(
        let date = $('.datepicker-time').val();

        let today = new Date(date);
        let months = ["January", "February", "March", "April", "May", "June", "July",
                    "August", "September", "October", "November", "December"];
        message = 'Please select ' + months[today.getMonth()] + ' ' + today.getDate();

        // Only the selected date is editable.
        let date1 = t.getYear() + '-' + t.getMonth() + '-' + t.getDate();
        let date2 = today.getYear() + '-' + today.getMonth() + '-' + today.getDate();
		var valid = (date1 == date2);
		var _class = '';
		var _tooltip = valid ? '' : message;
		return [valid,_class,_tooltip];
	}
});

$('#afternoonin').dateRangePicker({
	separator : ' ~ ',
    singleDate: true,
    showShortcuts: false,
    singleMonth: true,
	format: 'YYYY-MM-DD HH:mm:ss',
	autoClose: false,
	time: {
		enabled: true
    },
    beforeShowDay: function(t)
	{
        // Repeating this for every datepicker object otherwise I get an error.
        // There's probably a better way to do this.
        // Yes, this is very bad :(
        let date = $('.datepicker-time').val();

        let today = new Date(date);
        let months = ["January", "February", "March", "April", "May", "June", "July",
                    "August", "September", "October", "November", "December"];
        message = 'Please select ' + months[today.getMonth()] + ' ' + today.getDate();

        // Only the selected date is editable.
        let date1 = t.getYear() + '-' + t.getMonth() + '-' + t.getDate();
        let date2 = today.getYear() + '-' + today.getMonth() + '-' + today.getDate();
		var valid = (date1 == date2);
		var _class = '';
		var _tooltip = valid ? '' : message;
		return [valid,_class,_tooltip];
	}
});

$('#afternoonout').dateRangePicker({
	separator : ' ~ ',
    singleDate: true,
    showShortcuts: false,
    singleMonth: true,
	format: 'YYYY-MM-DD HH:mm:ss',
	autoClose: false,
	time: {
		enabled: true
    },
    beforeShowDay: function(t)
	{
        // Repeating this for every datepicker object otherwise I get an error.
        // There's probably a better way to do this.
        // Yes, this is very bad :(
        let date = $('.datepicker-time').val();

        let today = new Date(date);
        let months = ["January", "February", "March", "April", "May", "June", "July",
                    "August", "September", "October", "November", "December"];
        message = 'Please select ' + months[today.getMonth()] + ' ' + today.getDate();

        // Only the selected date is editable.
        let date1 = t.getYear() + '-' + t.getMonth() + '-' + t.getDate();
        let date2 = today.getYear() + '-' + today.getMonth() + '-' + today.getDate();
		var valid = (date1 == date2);
		var _class = '';
		var _tooltip = valid ? '' : message;
		return [valid,_class,_tooltip];
	}
});

$('.form-control').click(function() {
    $('.form-control').removeClass('is-valid');
});

$('#mid_day_break_from').dateRangePicker({
	separator : ' ~ ',
    singleDate: true,
    showShortcuts: false,
    singleMonth: true,
	format: 'YYYY-MM-DD HH:mm:ss',
	autoClose: false,
	time: {
		enabled: true
    },
    beforeShowDay: function(t)
	{
        // Repeating this for every datepicker object otherwise I get an error.
        // There's probably a better way to do this.
        // Yes, this is very bad :(
        let date = $('.datepicker-time').val();

        let today = new Date(date);
        let months = ["January", "February", "March", "April", "May", "June", "July",
                    "August", "September", "October", "November", "December"];
        message = 'Please select ' + months[today.getMonth()] + ' ' + today.getDate();

        // Only the selected date is editable.
        let date1 = t.getYear() + '-' + t.getMonth() + '-' + t.getDate();
        let date2 = today.getYear() + '-' + today.getMonth() + '-' + today.getDate();
		var valid = (date1 == date2);
		var _class = '';
		var _tooltip = valid ? '' : message;
		return [valid,_class,_tooltip];
	}
});

$('#mid_day_break_to').dateRangePicker({
	separator : ' ~ ',
    singleDate: true,
    showShortcuts: false,
    singleMonth: true,
	format: 'YYYY-MM-DD HH:mm:ss',
	autoClose: false,
	time: {
		enabled: true
    },
    beforeShowDay: function(t)
	{
        // Repeating this for every datepicker object otherwise I get an error.
        // There's probably a better way to do this.
        // Yes, this is very bad :(
        let date = $('.datepicker-time').val();

        let today = new Date(date);
        let months = ["January", "February", "March", "April", "May", "June", "July",
                    "August", "September", "October", "November", "December"];
        message = 'Please select ' + months[today.getMonth()] + ' ' + today.getDate();

        // Only the selected date is editable.
        let date1 = t.getYear() + '-' + t.getMonth() + '-' + t.getDate();
        let date2 = today.getYear() + '-' + today.getMonth() + '-' + today.getDate();
		var valid = (date1 == date2);
		var _class = '';
		var _tooltip = valid ? '' : message;
		return [valid,_class,_tooltip];
	}
});

$('#arriving_late').dateRangePicker({
	separator : ' ~ ',
    singleDate: true,
    showShortcuts: false,
    singleMonth: true,
	format: 'YYYY-MM-DD HH:mm:ss',
	autoClose: false,
	time: {
		enabled: true
    },
    beforeShowDay: function(t)
	{
        // Repeating this for every datepicker object otherwise I get an error.
        // There's probably a better way to do this.
        // Yes, this is very bad :(
        let date = $('.datepicker-time').val();

        let today = new Date(date);
        let months = ["January", "February", "March", "April", "May", "June", "July",
                    "August", "September", "October", "November", "December"];
        message = 'Please select ' + months[today.getMonth()] + ' ' + today.getDate();

        // Only the selected date is editable.
        let date1 = t.getYear() + '-' + t.getMonth() + '-' + t.getDate();
        let date2 = today.getYear() + '-' + today.getMonth() + '-' + today.getDate();
		var valid = (date1 == date2);
		var _class = '';
		var _tooltip = valid ? '' : message;
		return [valid,_class,_tooltip];
	}
});

$('#leaving_early').dateRangePicker({
	separator : ' ~ ',
    singleDate: true,
    showShortcuts: false,
    singleMonth: true,
	format: 'YYYY-MM-DD HH:mm:ss',
	autoClose: false,
	time: {
		enabled: true
    },
    beforeShowDay: function(t)
	{
        // Repeating this for every datepicker object otherwise I get an error.
        // There's probably a better way to do this.
        // Yes, this is very bad :(
        let date = $('.datepicker-time').val();

        let today = new Date(date);
        let months = ["January", "February", "March", "April", "May", "June", "July",
                    "August", "September", "October", "November", "December"];
        message = 'Please select ' + months[today.getMonth()] + ' ' + today.getDate();

        // Only the selected date is editable.
        let date1 = t.getYear() + '-' + t.getMonth() + '-' + t.getDate();
        let date2 = today.getYear() + '-' + today.getMonth() + '-' + today.getDate();
		var valid = (date1 == date2);
		var _class = '';
		var _tooltip = valid ? '' : message;
		return [valid,_class,_tooltip];
	}
});

$('#arriving_early').dateRangePicker({
	separator : ' ~ ',
    singleDate: true,
    showShortcuts: false,
    singleMonth: true,
	format: 'YYYY-MM-DD HH:mm:ss',
	autoClose: false,
	time: {
		enabled: true
    },
    beforeShowDay: function(t)
	{
        // Repeating this for every datepicker object otherwise I get an error.
        // There's probably a better way to do this.
        // Yes, this is very bad :(
        let date = $('.datepicker-time').val();

        let today = new Date(date);
        let months = ["January", "February", "March", "April", "May", "June", "July",
                    "August", "September", "October", "November", "December"];
        message = 'Please select ' + months[today.getMonth()] + ' ' + today.getDate();

        // Only the selected date is editable.
        let date1 = t.getYear() + '-' + t.getMonth() + '-' + t.getDate();
        let date2 = today.getYear() + '-' + today.getMonth() + '-' + today.getDate();
		var valid = (date1 == date2);
		var _class = '';
		var _tooltip = valid ? '' : message;
		return [valid,_class,_tooltip];
	}
});

$(document).ready(function() {
    $('.date-picker-wrapper .footer').hide();
    $('.default-top').hide();

    let width = document.getElementsByClassName('form-control')[0].offsetWidth - 50;
    $('.date-picker-wrapper').width(width);
    // $('.month-wrapper').css('padding', function(index) {
    //     return '0 5rem';
    // });
    $('.month-wrapper').css('margin-top', function(index) {
        return '1rem';
    });
});