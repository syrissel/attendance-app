<?php
require_once('../../classes/classes.php');
require('../authenticate.php');
session_start();

if ($_GET && isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $date_range = isset($_GET['date_range']) ? filter_input(INPUT_GET, 'date_range', FILTER_SANITIZE_STRING) : null;
    $attendance = Attendance::findByID($id);
    $form_user = $attendance->getUser();

    try {
        $attendance->destroy();
    
    } catch (PDOException $e) {
        print $e.getMessage();
    }
}
?>
<h4 class="text-info"><a href="#" data-id="<?= $form_user->getID() ?>" class="text-decoration-none text-info user-link"><?= $form_user->getFullName() ?></a> <i style="font-size:20px;" class="fa fa-angle-double-right" aria-hidden="true"></i> <a data-id="<?= $form_user->getID() ?>" href="#" class="attendances-link text-decoration-none text-info">Attendances</a></h4>
<div class="alert alert-primary" role="alert">
  <span>Attendance record for <?= $form_user->getFullName() ?> - <?= date('F d, Y', strtotime($attendance->getIntendedDate())) ?> has been deleted.</span>
</div>
<?php if ($date_range): ?>
    <a href="employee_attendance_report.php?id=<?= $form_user->getID() . '&date_range=' . $date_range ?>">Back</a>
<?php else: ?>
    <a href="#" id="back-link" data-id="<?= $form_user->getID() ?>">Back</a>
<?php endif ?>

<script>
$('#back-link').click(function() {
    let url = 'partials/attendance_form.php';
    let get = $.get(url, {
        id: $(this).data('id')
    });

    get.done(function(data) {
        $('#user-form-container').html(data);
    });
});
</script>