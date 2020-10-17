// When attendances link is clicked, go back to search form.
$('.attendances-link').click(function() {
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

// When user link is clicked, go back to user form.
$('.user-link').click(function() {
    $('#spinner').show()

    let url = 'partials/user_form.php';

    let get = $.get(url, {
        id: $(this).data('id')
    });

    get.done(function(data) {
        $('#spinner').hide()
        $('#user-form-container').html(data);
    });
});
