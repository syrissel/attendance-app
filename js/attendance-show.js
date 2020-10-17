$(document).ready(function() {
    $('#attendance_form').submit(function() {
        event.preventDefault()
        let url = $(this).attr('action')
        let post = $.post(url, {
            date: $('#date').val(),
            id:   $('#id').val()
        })

        post.done(function(data) {
            $('.col-6').html(data)
        })
    })
})