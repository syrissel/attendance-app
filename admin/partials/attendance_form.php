<?php
require_once('../../classes/classes.php');
require('../authenticate.php');
session_start();

if ($_GET && isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $form_user = User::findByID($id);
}

?>
<h4 class="text-info"><a href="#" data-id="<?= $form_user->getID() ?>" class="text-decoration-none text-info user-link"><?= $form_user->getFullName() ?></a> <i style="font-size:20px;" class="fa fa-angle-double-right" aria-hidden="true"></i> Attendances</h4>
<form id="user-attendance-date-form" action="partials/user_attendance_form.php" method="post">
    <div class="form-group">
        <label for="date">Search by date</label>
        <input type="text" name="date" id="date" class="form-control" autocomplete="off" value="<?= date('Y-m-d') ?>">
    </div>
    <div class="form-group">
        <input type="hidden" name="id" id="id" value="<?= $form_user->getID() ?>">
        <input type="submit" value="Go" class="btn btn-primary">
    </div>
</form>

<script src="../../js/nav-links.js"></script>
<script>
$('#date').dateRangePicker({
    autoClose: true,
    singleDate: true,
    showShortcuts: false
});

$('#user-attendance-date-form').submit(function(event) {
    event.preventDefault();
    $('#spinner').show()

    let url = $(this).attr('action');
    let post = $.post(url, {
        date: $('#date').val(),
        id:   $('#id').val()
    });

    post.done(function(data) {
        $('#spinner').hide()
        $('#user-form-container').html(data);
    });

});
</script>
