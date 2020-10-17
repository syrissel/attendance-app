<div class="d-flex justify-content-center w-100">
    <u><h4 class="py-3 font-italic align-self-center">
        Guest Activity for
        <?= date('F d, Y', strtotime($start_date)) . ' - ' . date('F d, Y', strtotime($end_date))  ?>
    </u></h4>
    <div class="align-self-end ml-auto my-3">
        <button type="button" class="btn btn-link" id="print_button" onclick="window.print();return false;">Print</button>
    </div>
</div>
<table class="table table-striped table-hover">
    <thead>
    <tr>
        <th class="" scope="col">Name</th>
        <th class="text-center" scope="col">Date of visit</th>
        <th class="text-center" scope="col">Sign-in</th>
        <th class="text-center" scope="col">Sign-out</th>
        <th class="text-center" scope="col">Duration</th>
        <th class="text-center" scope="col">Organization</th>
    </tr>
    </thead>
    <tbody>
        <?php foreach ($array as $user_range): ?>
            <?php foreach ($user_range->getUsers() as $user): ?>
            <?php 
                $attendance = Attendance::createWithDate($user, $user_range->getDate()); 
            ?>
            <?php if ($attendance->getClockIn()): ?>
            <tr>
                <td><?= $user->getFullName() ?></td>
                <td class="text-center"><?= date('M j, Y', strtotime($user->getCreatedAt())) ?></td>
                <td class="text-center"><?= formatPunchInTime($attendance->getClockIn()) ?></td>
                <td class="text-center"><?= formatPunchInTime($attendance->getClockOut()) ?></td>
                <td class="text-center"><?= getSemanticHours(getTimeDiffInMinutes($attendance->getClockIn(), $attendance->getClockOut(), false)) ?></td>
                <td class="text-center"><?= $user->getOrganization() ?></td>
            </tr>
            <?php endif ?>
            <?php endforeach ?>
        <?php endforeach ?>
    </tbody>
</table>
