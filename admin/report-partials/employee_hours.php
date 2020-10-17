<!--Not used anymore-->
<table class="table table-striped table-hover" style="table-layout:fixed;">
    <thead>
        <tr>
            <th scope="col">Name</th>
            <?php foreach ($period as $date): ?>
            <th scope="col"><?= $date->format('D, j') ?></th>
            <?php endforeach ?>
        </tr>
    </thead>
    <tbody>

            <?php foreach ($ar as $a): ?>
                <tr>
            <td><b><?= $a->getUser()->getFullName() ?></b></td>
                <?php foreach ($a->getAttendances() as $att): ?>
                    <?php if ($att->checkAbsent()): ?>
                        <td>Away</td>
                    <?php elseif ($att->checkClockOut()): ?>
                        <td><p class="m-0 text-warning" data-delay=100 data-toggle="tooltip" data-placement="right" title="Missing clock-out"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></p></td>
                    <?php else: ?>
                        <td><?= getTotalActualHours($att->getTotalPaidMinutes()) ?></td>
                    <?php endif ?>
                <?php endforeach ?>
                </tr>
            <?php endforeach ?>

    </tbody>
</table>