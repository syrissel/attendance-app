$('.clockpicker').clockpicker()
	.find('input').change(function(){
        if ($(this).val() != '') {
            // if the current input value contains the date, remove it (always 10 characters).
            let time = (this.value.length > 5) ? this.value.substring(11, 16) : this.value;
            this.value = $('#current_date').val() + ' ' + time + ':00';
        }
    })
    
function clearInput(ele) {
    ele.previousElementSibling.value = ""
}
