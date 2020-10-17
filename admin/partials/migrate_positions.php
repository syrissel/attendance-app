<?php
require_once('../../classes/classes.php');
require('../authenticate.php');
session_start();

if (isset($_POST['from_position']) && isset($_POST['to_position'])) {
    $from_position = filter_input(INPUT_POST, 'from_position', FILTER_SANITIZE_NUMBER_INT);
    $to_position = filter_input(INPUT_POST, 'to_position', FILTER_SANITIZE_NUMBER_INT);

    $users = User::findByPosition($from_position);

    foreach ($users as $user) {
        $user->updatePosition($to_position);
    }
}
?>

<h4 class="text-info mb-4">Migrate Staff Positions</h4>
<div id="confirmation"></div>
<form id="migrate_positions_form" action="partials/migrate_positions.php" method="post">
    <div class="form-row">
        <div class="form-group col-6">
            From
            <?php foreach(UserPosition::getAllPositions() as $position): ?>
                <div class="form-check">
                    <input type="radio" name="from_position" id="<?= $position->getPosition() ?>_from" value="<?= $position->getID() ?>">
                    <label class="form-check-label" for="<?= $position->getPosition() ?>_from"><?= $position->getPosition() ?></label>
                </div>
            <?php endforeach ?>
        </div>
        <div class="form-group col-6">
            To
            <?php foreach(UserPosition::getAllPositions() as $position): ?>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="to_position" id="<?= $position->getPosition() ?>_to" value="<?= $position->getID() ?>">
                    <label class="form-check-label" for="<?= $position->getPosition() ?>_to"><?= $position->getPosition() ?></label>
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
    let url = 'partials/staff_positions.php';
    let get = $.get(url);
    get.done(function(data) {
        $('#user-form-container').html(data);
    })
})

$('#migrate_positions_form').submit(function(event) {
    event.preventDefault();

    let url = 'partials/migrate_positions.php';
    let post = $.post(url, {
        from_position: $('input[name=from_position]:checked').val(),
        to_position:   $('input[name=to_position]:checked').val()
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