<div class="d-flex justify-content-center w-100">
    <u><h4 class="py-3 font-italic align-self-center">
        Attendance for
        <?= date('F d, Y', strtotime($start_date)) . ' - ' . date('F d, Y', strtotime($end_date))  ?>
    </u></h4>
    <div class="align-self-end ml-auto my-3">
        <button type="button" class="btn btn-link" id="print_button" onclick="window.print();return false;">Print</button>
    </div>
</div>
<table class="table table-bordered table-striped table-hover">
    <thead>
        <tr>
        <th>
            <?php if (Attendance::areMonthsEqual($formatted_start_date, $formatted_end_date)): ?>
                <?= $formatted_start_date ?>
            <?php else: ?>
                <?= $formatted_start_date . '/' . $formatted_end_date ?>
            <?php endif ?>  
        </th>
        <?php foreach ($period as $day): ?>
            <th><?= $day->format('D') . ', ' . $day->format('d') ?></th>
        <?php endforeach ?>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($array as $range): ?>
        <tr>
            <td><?= $range->getUser()->getFullName() ?></td>
        <?php foreach ($range->getAttendances() as $a): ?>
            <?php if ($a->getPartialDay()): ?>
                <td class="p-0">
                    <div class="partial_day_container">
                        <div class="row text-center text-secondary">
                            <div class="col">
                                <small class="font-weight-bold">
                                    Away <?= $a->getPartialDay() ?>
                                </small>
                            </div>
                        </div><!--row-->
                        
                        <div class="row text-center text-secondary">
                            <div class="col">
                                <small class="font-weight-bold">
                                    <?php if ($a->getClockIn()): ?>
                                    In at <?= $a->getClockIn() ? date('H:i', strtotime($a->getClockIn())) : '' ?>
                                    <?php else: ?>
                                    -
                                    <?php endif ?>
                                </small>
                            </div>
                        </div><!--row-->
                        
                    </div><!--partial_day_container-->
                </td>
            <?php elseif ($a->getAbsentReasonID() && $a->isFullDay()): ?>
                <td><p class='m-0 text-center text-secondary font-weight-bold'><?= AbsentReason::findByID($a->getAbsentReasonID())->getReason() ?></p></td>
            <?php else: ?>
                <?php if ($a->isLate()): ?>
                    <td>
                        <p class='m-0 text-center font-weight-bold bs-tooltip' style='font-size:16px;' data-toggle="manual" data-placement="right" title="<?= $a->getArriveLate() ? "Was due to arrive at ". date('H:i', strtotime($a->getArriveLate())) : 'Was due to arrive at ' . date('H:i', strtotime($a->getUser()->getExpectedClockIn())) ?>">
                            <a class="text-danger" href="attendance_show.php?id=<?= $a->getUser()->getID() ?>&date=<?= $a->getIntendedDate() ?>" target="_blank">
                                <?= $a->getClockIn() ? date('H:i', strtotime($a->getClockIn())) : '' ?>
                            </a>
                        </p>
                    </td>
                <?php else: ?>
                    <td>
                        <p class='m-0 text-center text-success font-weight-bold' style='font-size:16px;'>
                            <a class="text-success" href="attendance_show.php?id=<?= $a->getUser()->getID() ?>&date=<?= $a->getIntendedDate() ?>" target="_blank">
                                <?= $a->getClockIn() ? date('H:i', strtotime($a->getClockIn())) : '' ?>
                            </a>
                        </p>
                    </td>
                <?php endif ?>
            <?php endif ?>
        <?php endforeach ?>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>