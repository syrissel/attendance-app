<div class="d-flex justify-content-center w-100">
    <u><h4 class="py-3 font-italic align-self-center">
        Daily Report for
        <?= date('l, F d, Y', strtotime($date)) ?>
    </u></h4>
    <div class="align-self-end ml-auto my-3">
        <button type="button" class="btn btn-link" id="print_button" onclick="window.print();return false;">Print</button>
    </div>
</div>
<table class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
            <th scope="col">Name</th>
            <th scope="col">Clock In</th>
            <th scope="col">Clock Out</th>
            <th scope="col">Morning Out</th>
            <th scope="col">Morning In</th>
            <th scope="col">Lunch Out</th>
            <th scope="col">Lunch In</th>
            <th scope="col">Afternoon Out</th>
            <th scope="col">Afternoon In</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($att_array as $a): ?>
                <tr>
                    <td><?= $a->getUser()->getFullName() ?> <?= ($a->getPartialDay() && $a->getAbsentReasonID()) ? '<span class="notes"><i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" data-placement="right" title="' . AbsentReason::findByID($a->getAbsentReasonID())->getReason() . ' from ' . $a->getPartialDay() . '"></i></span>' : '' ?></td>
                    <?php if ($a->getAbsentReasonID() && $a->isFullDay()): ?>
                        <td colspan="8" class="text-center"><h5><?= AbsentReason::findByID($a->getAbsentReasonID())->getReason() ?></h5></td>
                    <?php else: ?>
                        <td class="text-center"><?= formatPunchInTime($a->getClockIn()) ?></td>
                        <td class="text-center"><?= formatPunchInTime($a->getClockOut()) ?></td>
                        <td class="text-center"><?= formatPunchInTime($a->getMorningOut()) ?></td>
                        <td class="text-center"><?= formatPunchInTime($a->getMorningIn()) ?></td>
                        <td class="text-center"><?= formatPunchInTime($a->getLunchOut()) ?></td>
                        <td class="text-center"><?= formatPunchInTime($a->getLunchIn()) ?></td>
                        <td class="text-center"><?= formatPunchInTime($a->getAfternoonOut()) ?></td>
                        <td class="text-center"><?= formatPunchInTime($a->getAfternoonIn()) ?></td>
                    <?php endif ?>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    <?php if ($has_absence): ?>
        <div class="alert alert-secondary w-100" role="alert" style="break-inside:avoid;">
        <h5>Notes</h5>
        <ul>
        <?php foreach ($att_array as $a): ?>
            <?php if ($a->getPartialDay() && $a->getAbsentReasonID()): ?>
            <li><?= $a->getUser()->getFirstName() . ' - ' . AbsentReason::findByID($a->getAbsentReasonID())->getReason() . ' from ' . $a->getPartialDay() ?><?= ($a->getPTO() != 0) ? " | PTO: ". minutesToHours($a->getPTO()) ." hours." : '' ?></li>
            <?php endif ?>
            <?php if ($a->getAbsentReasonID() && $a->isFullDay()): ?>
            <li><?= $a->getUser()->getFirstName() . ' - ' . AbsentReason::findByID($a->getAbsentReasonID())->getReason() . ' all day' ?><?= ($a->getPTO() != 0) ? " | PTO: " . minutesToHours($a->getPTO()) ." hours." : '' ?></li>
            <?php endif ?>
            <?php if ($a->getNonPaidIn() && $a->getNonPaidOut()): ?>
            <li><?= "{$a->getUser()->getFirstName()} - unpaid break from " . formatPunchInTime($a->getNonPaidOut()) . ' to ' . formatPunchInTime($a->getNonPaidIn()) . ' - ' .  minutesToHours($a->getTotalNonPaidMinutes()) . ' hours' ?></li>
            <?php endif ?>
            <?php if ($a->getLeaveEarly()): ?>
                <li><?= $a->getUser()->getFirstName() . ' is leaving at ' . date('H:i', strtotime($a->getLeaveEarly())) ?></li>
            <?php endif ?>
            <?php if ($a->getArriveLate()): ?>
                <li><?= $a->getUser()->getFirstName() . ' is arriving at ' . date('H:i', strtotime($a->getArriveLate())) ?></li>
            <?php endif ?>
            <?php if ($a->getArriveEarly()): ?>
                <li><?= $a->getUser()->getFirstName() . ' is arriving at ' . date('H:i', strtotime($a->getArriveEarly())) ?></li>
            <?php endif ?>
        <?php endforeach ?>
        </ul>
        </div>
    <?php endif ?>