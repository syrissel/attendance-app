<?php
require_once('../../classes/classes.php');
require('../authenticate.php');
session_start();

if (isset($_POST['from_reason']) && isset($_POST['to_reason'])) {
    $from_reason = filter_input(INPUT_POST, 'from_reason', FILTER_SANITIZE_NUMBER_INT);
    $to_reason = filter_input(INPUT_POST, 'to_reason', FILTER_SANITIZE_NUMBER_INT);

    $attendances = Attendance::findByReason($from_reason);

    foreach ($attendances as $a) {
        $a->updateReason($to_reason);
    }
}
?>

<h4 class="text-info mb-4">Migrate Absent Reasons</h4>
<div id="confirmation"></div>
<form id="migrate_reasons_form" action="partials/migrate_reasons.php" method="post">
    <div class="form-row">
        <div class="form-group col-6">
            From
            <?php foreach(AbsentReason::getAllReasons() as $reason): ?>
                <div class="form-check">
                    <input type="radio" name="from_reason" id="<?= $reason->getReason() ?>_from" value="<?= $reason->getID() ?>">
                    <label class="form-check-label" for="<?= $reason->getReason() ?>_from"><?= $reason->getReason() ?></label>
                </div>
            <?php endforeach ?>
        </div>
        <div class="form-group col-6">
            To
            <?php foreach(AbsentReason::getAllReasons() as $reason): ?>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="to_reason" id="<?= $reason->getReason() ?>_to" value="<?= $reason->getID() ?>">
                    <label class="form-check-label" for="<?= $reason->getReason() ?>_to"><?= $reason->getReason() ?></label>
                </div>
            <?php endforeach ?>
        </div>
    </div>
    <div class="form-group">
        <input type="submit" name="submit" value="Submit" class="btn btn-primary">
    </div> 
</form>
<a href="#" id="back_button">Back</a>

<script>
$('#back_button').click(function() {
    let url = 'partials/absent_reasons.php';
    let get = $.get(url);
    get.done(function(data) {
        $('#user-form-container').html(data);
    })
})

$('#migrate_reasons_form').submit(function(event) {
    event.preventDefault();

    let url = 'partials/migrate_reasons.php';
    let post = $.post(url, {
        from_reason: $('input[name=from_reason]:checked').val(),
        to_reason:   $('input[name=to_reason]:checked').val()
    });

    post.done(function(data) {
        $('#confirmation').show()
        $('#confirmation').html('<h5 class="text-success">Changes saved!</h5>')
        setTimeout(() => {
            $('#confirmation').hide();
        }, 2000);
    });
});
</script>
