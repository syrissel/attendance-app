<span id="error_info" class="text-danger"></span>
<form id="user-attendance-form" action="partials/user_attendance_form.php" method="post">
    <div class="form-group">
        <label for="clockin">Clock In</label>
        <div class="input-group clockpicker" data-placement="bottom" data-align="top" data-autoclose="true" data-default="now">
            <input type="text" name="clockin" id="clockin" class="form-control attendance-field" autocomplete="off" <?= $attendance->getAbsentReasonID() && !$attendance->getPartialDay() ? 'disabled' : '' ?> value="<?= $attendance->getClockIn() ?>">
        </div>
        <div id="clockin-err" class="text-danger"></div>
        <div class="absent-feedback text-info text-form">This field is intentionally left blank.</div>
    </div>
    <div class="form-group">
        <label for="clockout">Clock Out</label>
        <div class="input-group clockpicker" data-placement="bottom" data-align="top" data-autoclose="true" data-default="now">
            <input type="text" name="clockout" id="clockout" class="form-control attendance-field" autocomplete="off" <?= $attendance->getAbsentReasonID() && !$attendance->getPartialDay() ? 'disabled' : '' ?> value="<?= $attendance->getClockOut() ?>">
        </div>
        <div class="absent-feedback text-info text-form">This field is intentionally left blank.</div>
    </div>
    <div class="form-group">
        <label for="morningout">Morning Out</label>
        <div class="input-group clockpicker" data-placement="bottom" data-align="top" data-autoclose="true" data-default="now">
            <input type="text" name="morningout" id="morningout" class="form-control attendance-field" autocomplete="off" <?= $attendance->getAbsentReasonID() && !$attendance->getPartialDay() ? 'disabled' : '' ?> value="<?= $attendance->getMorningOut() ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="morningin">Morning In</label>
        <div class="input-group clockpicker" data-placement="bottom" data-align="top" data-autoclose="true" data-default="now">
            <input type="text" name="morningin" id="morningin" class="form-control attendance-field" autocomplete="off" <?= $attendance->getAbsentReasonID() && !$attendance->getPartialDay() ? 'disabled' : '' ?> value="<?= $attendance->getMorningIn() ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="lunchout">Lunch Out</label>
        <div class="input-group clockpicker" data-placement="bottom" data-align="top" data-autoclose="true" data-default="now">
            <input type="text" name="lunchout" id="lunchout" class="form-control attendance-field" autocomplete="off" <?= $attendance->getAbsentReasonID() && !$attendance->getPartialDay() ? 'disabled' : '' ?> value="<?= $attendance->getLunchOut() ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="lunchin">Lunch In</label>
        <div class="input-group clockpicker" data-placement="bottom" data-align="top" data-autoclose="true" data-default="now">
            <input type="text" name="lunchin" id="lunchin" class="form-control attendance-field" autocomplete="off" <?= $attendance->getAbsentReasonID() && !$attendance->getPartialDay() ? 'disabled' : '' ?> value="<?= $attendance->getLunchIn() ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="afternoonout">Afternoon Out</label>
        <div class="input-group clockpicker" data-placement="bottom" data-align="top" data-autoclose="true" data-default="now">
            <input type="text" name="afternoonout" id="afternoonout" class="form-control attendance-field" autocomplete="off" <?= $attendance->getAbsentReasonID() && !$attendance->getPartialDay() ? 'disabled' : '' ?> value="<?= $attendance->getAfternoonOut() ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="afternoonin">Afternoon In</label>
        <div class="input-group clockpicker" data-placement="bottom" data-align="top" data-autoclose="true" data-default="now">
            <input type="text" name="afternoonin" id="afternoonin" class="form-control attendance-field" autocomplete="off" <?= $attendance->getAbsentReasonID() && !$attendance->getPartialDay() ? 'disabled' : '' ?> value="<?= $attendance->getAfternoonIn() ?>">
        </div>
    </div>
    <hr />
    <div class="form-check">
        <input type="checkbox" name="absent" id="absent" <?= ($attendance->getAbsentReasonID() || $attendance->getPartialDay()) ? 'checked' : '' ?>>
        <label for="absent">Absent/Late</label>
    </div>
    <div id="reason-group">
        <div class="form-group">
            <label for="reason">Reason</label>
            <select name="reason" id="reason" value="<?= $attendance->getAbsentReasonID() ?>" class="form-control <?= !empty($reason_err) ? 'is-invalid' : '' ?>">
                <option value="" selected></option>
                
                <?php foreach(AbsentReason::getAllReasons() as $reason): ?>
                    <option value="<?= $reason->getID() ?>" <?= ($reason->getID() == $attendance->getAbsentReasonID()) ? 'selected' : '' ?>><?= $reason->getReason() ?></option>
                <?php endforeach ?>
            </select>
            <div id="reason_err" class="text-danger"></div>
        </div>
        <hr />
        <div id="reason_radio_container"><!--hide on document load-->
            <div class="d-flex justify-content-center" id="reason_radio_group">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="reason_radio" id="full_day_rad" value="full">
                    <label class="form-check-label" for="full_day_rad">
                        Full Day
                    </label>
                </div>
                <div class="form-check ml-2">
                    <input class="form-check-input" type="radio" name="reason_radio" id="partial_day_rad" value="partial">
                    <label class="form-check-label" for="partial_day_rad">
                        Other
                    </label>
                </div>
                <div class="form-check ml-2" id="pto_form_group">
                    <input class="form-check-input" type="checkbox" name="pto_check" id="pto_check" value="pto">
                    <label class="form-check-label" for="pto_check">
                        PTO
                    </label>
                </div>
            </div>
        </div>
        <span class="text-danger" id="reason_radio_err"></span>
        <div class="form-group" id="pto_group">
            <label for="pto">PTO Hours</label>
            <input type="text" name="pto" id="pto" class="form-control" value="<?= minutesToHours($attendance->getPTO()) ?>">
            <small class="text-muted text-form">If you want to remove PTO hours, enter 0.</small>
            <span class="text-danger" id="pto_err"></span>
        </div>


        <hr />
        <div id="partial_day_group" class="form-group">
            <div class="d-flex justify-content-center">
                <div class="form-check form-check-inline" id="">
                    <input class="form-check-input partial-check" type="checkbox" name="unpaid_check" id="unpaid_check" value="<?= $attendance->getNonPaidIn() ?>">
                    <label class="form-check-label" for="unpaid_check">
                        Unpaid Break
                    </label>
                </div>
                <div class="form-check form-check-inline ml-2 partial-check" id="">
                    <input class="form-check-input" type="checkbox" name="arriving_early_check" id="arriving_early_check" value="<?= $attendance->getArriveEarly() ?>">
                    <label class="form-check-label" for="arriving_early_check">
                        Arriving Early
                    </label>
                </div>
                <div class="form-check form-check-inline ml-2 partial-check" id="">
                    <input class="form-check-input" type="checkbox" name="arriving_late_check" id="arriving_late_check" value="<?= $attendance->getArriveLate() ?>">
                    <label class="form-check-label" for="arriving_late_check">
                        Arriving Late
                    </label>
                </div>
                <div class="form-check form-check-inline ml-2 partial-check" id="">
                    <input class="form-check-input" type="checkbox" name="leaving_early_check" id="leaving_early_check" value="<?= $attendance->getLeaveEarly() ?>">
                    <label class="form-check-label" for="leaving_early_check">
                        Leaving Early
                    </label>
                </div>
            </div><!--d-flex-->
            <hr>
            <div id="mid_day_break_group" class="form-group d-none partial-options-group border p-2">
                <label for="">Unpaid Break</label><br />
                <div class="form-row">
                    <div class="col">
                        <label for="mid_day_break_from">From:</label>
                        <div class="input-group clockpicker" data-placement="top" data-align="top" data-autoclose="true" data-default="now">
                            <input type="text" name="mid_day_break_from" id="mid_day_break_from" class="form-control partial-field" autocomplete="off" value="<?= $attendance->getNonPaidOut() ?>">
                        </div>
                    </div>
                    <div class="col">
                        <label for="mid_day_break_to">To:</label>
                        <div class="input-group clockpicker" data-placement="top" data-align="top" data-autoclose="true" data-default="now">
                            <input type="text" name="mid_day_break_to" id="mid_day_break_to" class="form-control partial-field" autocomplete="off" value="<?= $attendance->getNonPaidIn() ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div id="arriving_early_group" class="form-group d-none partial-options-group">
                <label for="arriving_early">Arriving Early</label>
                <div class="input-group clockpicker" data-placement="top" data-align="top" data-autoclose="true" data-default="now">
                    <input type="text" name="arriving_early" id="arriving_early" class="form-control partial-field" placeholder="Select time" autocomplete="off" value="<?= $attendance->getArriveEarly() ?>">
                </div>
            </div>
            <div id="arriving_late_group" class="form-group d-none partial-options-group">
                <label for="arriving_late">Arriving Late</label>
                <div class="input-group clockpicker" data-placement="top" data-align="top" data-autoclose="true" data-default="now">
                    <input type="text" name="arriving_late" id="arriving_late" class="form-control partial-field" placeholder="Select time" autocomplete="off" value="<?= $attendance->getArriveLate() ?>">
                </div>
            </div>
            <div id="leaving_early_group" class="form-group d-none partial-options-group">
                <label for="leaving_early">Leaving Early</label>
                <div class="input-group clockpicker" data-placement="top" data-align="top" data-autoclose="true" data-default="now">
                    <input type="text" name="leaving_early" id="leaving_early" class="form-control partial-field" placeholder="Select time" autocomplete="off" value="<?= $attendance->getLeaveEarly() ?>">
                </div>
            </div>
        </div>
    </div><!--reason-group-->
    <input type="hidden" name="id" id="id" value="<?= $form_user->getID() ?>">
        <input type="hidden" class="datepicker-time" value="<?= date('Y-m-d H:i:s', strtotime($attendance->getIntendedDate())) ?>">
        <input type="hidden" id="sticky_clockin" name="sticky_clockin" value="<?= $attendance->getClockIn() ? date('Y-m-d H:i:s', strtotime($attendance->getClockIn())) : '' ?>">
        <input type="hidden" id="sticky_clockout" name="sticky_clockout" value="<?= $attendance->getClockOut() ? date('Y-m-d H:i:s', strtotime($attendance->getClockOut())) : '' ?>">
        <input type="hidden" id="intended_date" name="intended_date" value="<?= date('Y-m-d H:i:s', strtotime($attendance->getIntendedDate())) ?>">
        <input type="hidden" id="sticky_reason" data-reason="<?= AbsentReason::findByID($attendance->getAbsentReasonID())->getReason() ?>">
        <input type="hidden" name="partial_start_time" id="partial_start_time" value="<?= $partial_start_time ?>">
        <input type="hidden" name="partial_end_time" id="partial_end_time" value="<?= $partial_end_time ?>">
        <input type="hidden" name="date_range" id="date_range" value="<?= $date_range ?>">
        <input type="hidden" name="current_page" id="current_page" value="<?= $current_page ?>">
        <input type="hidden" name="current_date" id="current_date" value="<?= date('Y-m-d', strtotime($attendance->getIntendedDate())) ?>">
        <?php if (isset($_GET['calendar'])): ?>
            <input type="hidden" name="calendar" value="true">
        <?php endif ?>
    <div class="form-group mt-4">
        <input type="submit" value="Submit" class="btn btn-primary float-left mr-2">

        <div class="btn-group float-right">
            <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Delete
            </button>
            <div class="dropdown-menu">
                <a href="#" class="dropdown-item" id="delete-attendance" data-id="<?= $attendance->getID() ?>">Delete</a>
            </div>
        </div>
        <?php if ($current_page == 'edit_attendance.php'): ?>
            <input type="button" value="Back" class="btn btn-secondary float-right mr-1" onclick="window.history.back()">
        <?php endif ?>
    </div>
</form>