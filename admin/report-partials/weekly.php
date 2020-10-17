<div class="d-flex justify-content-center w-100">
    <u><h4 class="py-3 font-italic align-self-center">
        Daily Reports from
        <?= date('F d, Y', strtotime($start_date)) . ' to ' . date('F d, Y', strtotime($end_date))  ?>
    </u></h4>
    <div class="align-self-end ml-auto my-3">
        <button type="button" class="btn btn-link" id="print_button" onclick="window.print();return false;">Print</button>
    </div>
</div>
<?php foreach ($array as $date): ?>
    <div class="page-break w-100 my-4">
    <table class="table table-bordered table-striped table-hover">
    <tbody>
    <thead>
        <?php $date_text = date('l, F d, Y', strtotime($date[0])); ?>
        <div class="date-heading pl-1" colspan="9"><h5><strong><u><?= $date_text ?></u></strong></h5></div>
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
    <?php foreach ($date[1] as $user): ?>
    <tr>
    <th scope="row"><?= $user['first_name'] . ' ' . $user['last_name'] ?></th>
    <?php $a = Attendance::createWithDate(User::findByID($user['id']), new DateTime($date[0])) ?>
    <?php if ($a->getAbsentReasonID() && $a->isFullDay()): ?>
        <td colspan="8" class="text-center"><h5><?= AbsentReason::findByID($a->getAbsentReasonID())->getReason() ?></h5></td>
    <?php else: ?>
    <td class="text-center"><?= formatPunchInTime($user['clock_in']) ?></td>
    <td class="text-center"><?= formatPunchInTime($user['clock_out']) ?></td>
    <td class="text-center"><?= formatPunchInTime($user['morning_out']) ?></td>
    <td class="text-center"><?= formatPunchInTime($user['morning_in']) ?></td>
    <td class="text-center"><?= formatPunchInTime($user['lunch_out']) ?></td>
    <td class="text-center"><?= formatPunchInTime($user['lunch_in']) ?></td>
    <td class="text-center"><?= formatPunchInTime($user['afternoon_out']) ?></td>
    <td class="text-center"><?= formatPunchInTime($user['afternoon_in']) ?></td>
    <?php endif ?>
    </tr>
    <?php endforeach ?>
    </tbody>
    </table>
    <div class="alert alert-secondary w-100" role="alert">
    <h5>Notes</h5>
    <?php foreach ($date[1] as $user): ?>
    <?php $a = Attendance::createWithDate(User::findByID($user['id']), new DateTime($date[0])) ?>
        <ul>
            <?php if ($a->getPartialDay() && $a->getAbsentReasonID()): ?>
            <li><?= $a->getUser()->getFirstName() . ' - ' . AbsentReason::findByID($a->getAbsentReasonID())->getReason() . ' from ' . $a->getPartialDay() ?><?= ($a->getPTO() != 0) ? " | PTO: ". minutesToHours($a->getPTO()) . " hours." : '' ?></li>
            <?php endif ?>
            <?php if ($a->getAbsentReasonID() && $a->isFullDay()): ?>
            <li><?= $a->getUser()->getFirstName() . ' - ' . AbsentReason::findByID($a->getAbsentReasonID())->getReason() . ' all day' ?><?= ($a->getPTO() != 0) ? " | PTO: ". minutesToHours($a->getPTO()) ." hours." : '' ?></li>
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
        </ul>
    <?php endforeach ?>
    </div>
    </div><!--page-break-->
<?php endforeach ?>