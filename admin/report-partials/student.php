<div class="d-flex justify-content-center w-100">
    <u><h4 class="py-3 font-italic align-self-center">
        Student Biweekly Report from
        <?= date('F d, Y', strtotime($start_date)) . ' to ' . date('F d, Y', strtotime($end_date))  ?>
    </u></h4>
    <div class="align-self-end ml-auto my-3">
        <button type="button" class="btn btn-link" id="print_button" onclick="window.print();return false;">Print</button>
    </div>
</div>
<table class="table table-borderless table-striped table-hover">
        <thead>
            <tr>
            <th scope="col"><u>Name</u></th>
            <th scope="col"><u>Position</u></th>
            <th scope="col" class="text-center"><u>Hours</u></th>
            <th scope="col" class="text-center"><u>Comments</u></th>
            </tr>
        </thead>
        <tbody>
                <?php foreach ($student_ar as $range): ?>
                    <tr>
                        <td><b><a class="text-body" target="_blank" href="employee_attendance_report.php?id=<?= $range->getUser()->getID() ?>&date_range=<?= $start_date . '_' . $end_date ?>"><?= $range->getUser()->getFullName() ?></a></b></td>
                        <td><?= $range->getUser()->getUserPosition()->getPosition() ?></td>
                        <td class="text-center font-weight-bold"><?= minutesToHours($range->getTotalMinutes()) ?></td>
                        <td><input type="text" name="comments" class="comments w-100" style="border:none;"></td>
                    </tr>
                <?php endforeach ?>
            <tr>
                <td><b><?= count(User::getAllStudents()) ?> Student<?= count(User::getAllStudents()) > 1 ? 's' : '' ?></b></td>
                <th class="text-center">
                    <u>Total Hours</u>
                </th>
                <td class="text-center bg-light"><b><?= getTotalPaidHours(AttendanceRange::addTotalMinutes($student_ar)) ?></b></td>
            </tr>
        </tbody>
    </table>
