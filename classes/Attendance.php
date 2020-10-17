<?php
require_once('classes.php');

class AttendanceException extends Exception { }
/**
 * Class representing attendance table in database. Each instance will represent one date of
 * attendance for a specified user.
 * 
 * @author Steph Mireault
 * @date   July 20, 2020
 */
class Attendance {
    private $id;
    private $clockin;
    private $clockout;
    private $morningin;
    private $morningout;
    private $lunchin;
    private $lunchout;
    private $afternoonin;
    private $afternoonout;
    private $partial_day;
    private $intended_date;
    private $updated_at;
    private $created_at;
    private $user;
    private $user_id;
    private $total_minutes;
    private $absent_reason_id;
    private $pto;
    private $non_paid_in;
    private $non_paid_out;
    private $date;
    private $arrive_late;
    private $leave_early;
    private $arrive_early;
    private $is_estimate;

    // Getters
    public function getID()             { return $this->id; }
    public function getClockIn()        { return $this->clockin; }
    public function getClockOut()       { return $this->clockout; }
    public function getMorningIn()      { return $this->morningin; }
    public function getMorningOut()     { return $this->morningout; }
    public function getLunchIn()        { return $this->lunchin; }
    public function getLunchOut()       { return $this->lunchout; }
    public function getAfternoonIn()    { return $this->afternoonin; }
    public function getAfternoonOut()   { return $this->afternoonout; }
    public function getPartialDay()     { return $this->partial_day; }
    public function getAbsentReasonID() { return $this->absent_reason_id; }
    public function getIntendedDate()   { return $this->intended_date; }
    public function getUpdatedAt()      { return $this->updated_at; }
    public function getCreatedAt()      { return $this->created_at; }
    public function getUserID()         { return $this->user_id; }
    public function getUser()           { return $this->user; }
    public function getPTO()            { return $this->pto; }
    public function getNonPaidIn()      { return $this->non_paid_in; }
    public function getNonPaidOut()     { return $this->non_paid_out; }

    // The date field is only stored in the attendance object.
    public function getDate()           { return $this->date; }
    public function getArriveLate()     { return $this->arrive_late; }
    public function getLeaveEarly()     { return $this->leave_early; }
    public function getArriveEarly()    { return $this->arrive_early; }
    
    public function setAbsentReasonID($id) { $this->absent_reason_id = $id; }
    public function setClockOut($clock_out) { $this->clockout = $clock_out; }
    
    // Manipulate object's fields to get an estimation for payroll in the event all clock-outs haven't been logged.
    public function setEstimatedHours() {        
        $expected_clock_out = date('H:i:s', strtotime($this->user->getExpectedClockOut()));
        $expected_clock_in = date('H:i:s', strtotime($this->user->getExpectedClockIn()));
        $current_date = $this->date;
        $this->is_estimate = true;

        if ($current_date->format('D') != 'Sat' && $current_date->format('D') != 'Sun') {

            if (!$this->clockin) {
                if ($this->arrive_early) {
                    $this->clockin = $this->arrive_early;
                } else if ($this->arrive_late) {
                    $this->clockin = $this->arrive_late;
                } else {
                    $this->clockin = $current_date->format("Y-m-d $expected_clock_in");
                }
            }

            if (!$this->clockout && !$this->leave_early) {
                $this->clockout = $current_date->format("Y-m-d $expected_clock_out");
            }
        }
    }
    
    /**
     * The total amount of time spent in the building before deduction of unpaid breaks.
     *
     * @return void
     */
    public function getTotalPaidMinutes() {
        return getTimeDiffInMinutes($this->getRoundedClockIn(), $this->getRoundedClockOut());
    }

    public function getTotalEstimatedMinutes() {
        $expected_clock_out = date('H:i:s', strtotime($this->user->getExpectedClockOut()));
        $expected_clock_in = date('H:i:s', strtotime($this->user->getExpectedClockIn()));
        $current_date = $this->date;
        $this->is_estimate = true;
        $clock_in = '';
        $clock_out = '';

        if ($current_date->format('D') != 'Sat' && $current_date->format('D') != 'Sun') {

            if (!$this->clockin) {
                if ($this->arrive_early) {
                    $clock_in = $this->arrive_early;
                } else if ($this->arrive_late) {
                    $clock_in = $this->arrive_late;
                } else {
                    $clock_in = $current_date->format("Y-m-d $expected_clock_in");
                }
            } else {
                if (date('H:i:s', strtotime($this->clockin)) <= $expected_clock_in && !$this->arrive_early) {
                    $clock_in = $current_date->format("Y-m-d $expected_clock_in");
                } else if ($this->arrive_early && $this->clockin <= $this->arrive_early) {
                    $clock_in = $this->arrive_early;
                } else {
                    $clock_in = $this->clockin;
                }
            }

            if (!$this->clockout && !$this->leave_early) {
                $clock_out = $current_date->format("Y-m-d $expected_clock_out");
            } else if ($this->leave_early) {
                $clock_out = $this->leave_early;
            } else {
                $clock_out = $this->clockout;
            }
        }

        if ($this->absent_reason_id && $this->isFullDay()) {
            $result = 0;
        } else if ($this->pto > 0) {
            $result = getTimeDiffInMinutes(roundTimeStamp($clock_in), roundTimeStamp($clock_out));
        } else {
            $result = getTimeDiffInMinutes(roundTimeStamp($clock_in), roundTimeStamp($clock_out));
        }

        return $result;
    }

    public function getTotalNonPaidMinutes() {
        return getTimeDiffInMinutes($this->roundTime('non_paid_out'), $this->roundTime('non_paid_in'));
    }

    public function getTotalNetMinutes() {
        return $this->getTotalPaidMinutes() - $this->getTotalNonPaidMinutes();
    }

    public function getTotalNetEstimatedMinutes() {
        return $this->getTotalEstimatedMinutes() - $this->getTotalNonPaidMinutes();
    }

    public function getTotalWorkedMinutes() {
        return $this->getTotalPaidMinutes() - $this->getTotalNonPaidMinutes() + $this->getPTO();
    }

    public function getTotalHours() {
        $hours = (int)($this->getTotalMinutes() / 60);
        $minutes = $this->getTotalMinutes() % 60;
        $minutes = ($minutes < 10) ? ('0' . $minutes) : $minutes;
        return ($this->getTotalMinutes() > 0) ? '<b>' . $hours . 'h ' . $minutes . 'm ' . '</b>': $hours . 'h ' . $minutes . 'm ';
    }

    public function isFullDay() {
        return !$this->partial_day && !$this->arrive_early && !$this->arrive_late && !$this->leave_early && !$this->non_paid_in && !$this->non_paid_out;
    }


        /**
     * getTotalDayMinutes
     *
     * @param  DateTime $date
     * @return void
     */
    public function getTotalDayMinutes() {
        Database::query("SELECT clockin, clockout, TIMESTAMPDIFF(MINUTE, clockin, clockout) AS time_in_minutes 
                         FROM attendances 
                         WHERE DATE_FORMAT(clockin, '%Y%m%d') = DATE_FORMAT(:clockin, '%Y%m%d')
                         AND user_id = :id");
        Database::bind(':clockin', $this->getClockIn(), PDO::PARAM_STR);
        Database::bind(':id', $this->user->getID(), PDO::PARAM_INT);

        $row = Database::getRow();
        return $row['time_in_minutes'];
    }

            /**
     * getTotalDayMinutes
     *
     * @param  DateTime $date
     * @return void
     */
    public function roundTime($clock_type) {
        Database::query("SELECT DATE_ADD(
                                    DATE_FORMAT($clock_type, '%Y-%m-%d %H:00:00'),
                                    INTERVAL (CASE
                                                WHEN MINUTE($clock_type) <= 8 THEN 0
                                                WHEN MINUTE($clock_type) >= 9 && MINUTE($clock_type) <= 23 THEN 15
                                                WHEN MINUTE($clock_type) >= 24 && MINUTE($clock_type) <= 38 THEN 30
                                                WHEN MINUTE($clock_type) >= 39 && MINUTE($clock_type) <= 53 THEN 45
                                                ELSE 60
            
                                            END)
                                    MINUTE
                                ) AS formatted_date 
                        FROM attendances
                        WHERE DATE_FORMAT(intended_date, '%Y-%m-%d') = DATE_FORMAT(:intended_date, '%Y-%m-%d')
                        AND user_id = :id");
        //Database::bind('$clock_type', $clock_type, PDO::PARAM_STR);
        Database::bind(':intended_date', $this->getIntendedDate(), PDO::PARAM_STR);
        Database::bind(':id', $this->user->getID(), PDO::PARAM_INT);

        $row = Database::getRow();
        return date('H:i', strtotime($row['formatted_date']));
    }

    public function getRoundedClockIn() {

        $user = $this->user;
        $result = 0;
        $clockin = date('H:i', strtotime($this->clockin));
        $expected_clockin = date('H:i', strtotime($user->getExpectedClockIn()));

        if ($this->arrive_early && !$this->clockin) {
            $result = $this->roundTime('arrive_early');
        } else if ($this->arrive_late && !$this->clockin) {
            $result = $this->roundTime('arrive_late');
        } else if ($this->clockin) {
            if ($clockin <= $expected_clockin && !$this->arrive_early) {
                $result = $user->getExpectedClockIn();
            } else {
                $result = $this->roundTime('clockin');
            }
        }

        return $result;
    }

    public function getRoundedClockOut() {

        $user = $this->user;
        $result = 0;
        $clockin = date('H:i', strtotime($this->clockin));
        $expected_clockin = date('H:i', strtotime($user->getExpectedClockIn()));
        $clockout = date('H:i', strtotime($this->clockout));
        $expected_clockout = date('H:i', strtotime($user->getExpectedClockOut()));

        if ($this->clockout) {
            if (abs(getTimeDiffInMinutes($clockout, $expected_clockout, false)) < abs(15)) {
                $result = $user->getExpectedClockOut();
            } else {
                $result = $this->roundTime('clockout');
            }
        // If there isn't a clockout, set result to the clock-in so the time difference will be 0 (zero hours will be counted for that day)
        } else if ($this->arrive_early && !$this->clockin) {
            $result = $this->roundTime('arrive_early');
        } else if ($this->arrive_late && !$this->clockin) {
            $result = $this->roundTime('arrive_late');
        } else if ($this->clockin) {
            if ($clockin <= $expected_clockin && !$this->arrive_early) {
                $result = $user->getExpectedClockIn();
            } else {
                $result = $this->roundTime('clockin');
            }
        }
        
        return $result;
    }
    
    /**
     * Returns true if there is no clock-out and absent id for the record but there is a clock-in.
     *
     * @return void
     */
    public function checkClockOut() {
        return !$this->clockout && $this->clockin && !$this->absent_reason_id;
    }

    public function checkAbsent() {
        return !($this->absent_reason_id == null);
    }

    public function isLate() {
        $clock_in = date('H:i', strtotime($this->clockin));
        $arrive_late = date('H:i', strtotime($this->arrive_late));

        return $this->arrive_late ? $clock_in > $arrive_late : $clock_in > $this->user->getExpectedClockIn();
    }

    public function getLateStatus() {
        $time = $this->arrive_late ? date('H:i', strtotime($this->arrive_late)) : date('H:i', strtotime($this->user->getExpectedClockIn()));
        $format = ($this->isLate()) ? '<p class="m-0 text-danger bs-tooltip" data-toggle="manual" data-placement="right" title="Was due to arrive at ' . $time . '"><strong>' . date('H:i', strtotime($this->getClockIn())) . '</strong></p>' : '<p class="m-0 text-success text-center"><i class="fa fa-check"></i></p>' ;
        return ($this->getClockIn()) ? $format : '<p class="m-0 text-center">-</p>';
    }
        
    // Trying to do Factory pattern here.
    private function __construct() { }

    public static function createWithUser($user) {
        $instance = new self();
        $instance->user = $user;
        $instance->populate();
        return $instance;
    }

    public static function createWithDate($user, $date) {
        $instance = new self();
        $instance->user = $user;
        $instance->date = $date;

        // This only creates a hollow object in memory. Does not insert into db.
        // if ($instance->populateByDate($date) == null) {
        //     $instance->created_at = $date->format('Y-m-d');
        // }
        $instance->populateByDate($date);
        return $instance;
    }

    public static function createWithUserRow($user, $row) {
        $instance = self::createWithUser($user);
        $instance->fill($row);
        return $instance;
    }

    public static function findByID($id) {
        Database::query("SELECT * FROM attendances WHERE id = :id");
        Database::bind(':id', $id, PDO::PARAM_INT);
        $row = Database::getRow();
        $instance = new self();
        $instance->fill($row);
        $user = User::findByID($instance->getUserID());
        $instance->user = $user;
        return $instance;
    }

    public static function findByReason($reason_id) {
        Database::query("SELECT * FROM attendances WHERE `absent_reason_id` = :absent_reason_id");
        Database::bind(':absent_reason_id', $reason_id, PDO::PARAM_INT);
        $stmt = Database::getStatement();
        $attendances = [];

        while ($row = $stmt->fetch()) {
            array_push($attendances, self::createWithUserRow(User::findByID($row['user_id']), $row));
        }

        return $attendances;
    }

    public function updateReason($reason_id) {
        Database::query("UPDATE attendances SET absent_reason_id = :absent_reason_id WHERE id = :id");
        Database::bind(':absent_reason_id', $reason_id, PDO::PARAM_INT);
        Database::bind(':id', $this->id, PDO::PARAM_INT);
        Database::execute();
    }

    public function destroy() {
        Database::query("DELETE FROM attendances WHERE id = :id");
        Database::bind(':id', $this->id, PDO::PARAM_INT);
        Database::execute();
    }

    public static function createFullAttendance($user, $clockin, $clockout, $morningin, $morningout, $lunchin, $lunchout, $afternoonin, $afternoonout, $reason_id) {
        Database::query("INSERT INTO attendances (clockin, clockout, morningin, morningout, lunchin, lunchout, afternoonin, afternoonout, absent_reason_id, user_id)
                         VALUES                  (:clockin, :clockout, :morningin, :morningout, :lunchin, :lunchout, :afternoonin, :afternoonout, :absent_reason_id, :user_id)");
        $clockout = ($clockout === '') ? null : $clockout;
        $morningin = ($morningin === '') ? null : $morningin;
        $morningout = ($morningout === '') ? null : $morningout;
        $lunchin = ($lunchin === '') ? null : $lunchin;
        $lunchout = ($lunchout === '') ? null : $lunchout;
        $afternoonin = ($afternoonin === '') ? null : $afternoonin;
        $afternoonout = ($afternoonout === '') ? null : $afternoonout;
        $reason_id = ($reason_id == '') ? null : $reason_id;
        
        Database::bind(':clockin', $clockin, PDO::PARAM_STR);
        Database::bind(':clockout', $clockout, PDO::PARAM_STR);
        Database::bind(':morningin', $morningin, PDO::PARAM_STR);
        Database::bind(':morningout', $morningout, PDO::PARAM_STR);
        Database::bind(':lunchin', $lunchin, PDO::PARAM_STR);
        Database::bind(':lunchout', $lunchout, PDO::PARAM_STR);
        Database::bind(':afternoonin', $afternoonin, PDO::PARAM_STR);
        Database::bind(':afternoonout', $afternoonout, PDO::PARAM_STR);
        Database::bind(':absent_reason_id', $reason_id, PDO::PARAM_INT);
        Database::bind(':user_id', $user->getID(), PDO::PARAM_INT);
        Database::execute();

        return Attendance::getLast();
    }

    public static function getLast() {
        Database::query('SELECT * FROM attendances ORDER BY id DESC LIMIT 1');
        $row = Database::getRow();
        $instance = new self();
        $instance->fill($row);
        $user = User::findByID($instance->getUserID());
        $instance->user = $user;
        return $instance;
    }
    
    /**
     * Checks whether a record exists in the database. Only one record may exist per day per user.
     *
     * @param  User $user:   The user belonging to the record.
     * @param  String $date: The date to be checked.
     * @return void
     */
    public static function exists($user, $date) {
        Database::query("SELECT id FROM attendances 
                         WHERE DATE_FORMAT(intended_date, '%Y%m%d') = DATE_FORMAT(:intended_date, '%Y%m%d') 
                         AND user_id = :user_id LIMIT 1");
        Database::bind(':intended_date', $date, PDO::PARAM_STR);
        Database::bind(':user_id', $user->getID(), PDO::PARAM_INT);
        $stmt = Database::getStatement();
        $exists = false;

        if ($stmt->rowCount() > 0) {
            $exists = true;
        }

        return $exists;
    }

    public function updateAttendance($clockin_type, $time) {
        $time = ($time == '') ? null : $time;
        Database::query("UPDATE attendances SET $clockin_type = :param_time WHERE id = :id");
        Database::bind(':param_time', $time, PDO::PARAM_STR);
        Database::bind(':id', $this->id, PDO::PARAM_INT);
        Database::execute();
    }
    
    /**
     * Validates form data with fields pulled from the database. Ensures the passed form fields
     * match the fields saved in the database. If an update to the table was not successful,
     * an error will be shown to the user.
     *
     * @param  Attendance $attendance: The attendance object whose fields will be pulled from the db.
     * @param  Array $form_data:       The form array $_POST whose fields will be compared to the new attendance object.
     * @return void
     */
    public static function verifyFields($attendance, $form_data) {
        Database::query("SELECT * FROM attendances WHERE id = :id LIMIT 1");
        Database::bind(':id', $attendance->getID(), PDO::PARAM_INT);
        $row = Database::getRow();
        $verify_attendance = new self();
        $verify_attendance->fill($row);
        $valid = true;

        $clock_in = ($verify_attendance->getClockIn() == null) ? '' : $verify_attendance->getClockIn();
        $clock_out = ($verify_attendance->getClockOut() == null) ? '' : $verify_attendance->getClockOut();
        $morning_in = ($verify_attendance->getMorningIn() == null) ? '' : $verify_attendance->getMorningIn();
        $morning_out = ($verify_attendance->getMorningOut() == null) ? '' : $verify_attendance->getMorningOut();
        $lunch_in = ($verify_attendance->getLunchIn() == null) ? '' : $verify_attendance->getLunchIn();
        $lunch_out = ($verify_attendance->getLunchOut() == null) ? '' : $verify_attendance->getLunchOut();
        $afternoon_in = ($verify_attendance->getAfternoonIn() == null) ? '' : $verify_attendance->getAfternoonIn();
        $afternoon_out = ($verify_attendance->getAfternoonOut() == null) ? '' : $verify_attendance->getAfternoonOut();

        if ((trim($form_data['clockin']) != $clock_in) || (trim($form_data['clockout']) != $clock_out) || (trim($form_data['morningin']) != $morning_in) || (trim($form_data['morningout']) != $morning_out) || (trim($form_data['lunchin']) != $lunch_in) || (trim($form_data['lunchout']) != $lunch_out) || (trim($form_data['afternoonin']) != $afternoon_in) || (trim($form_data['afternoonout']) != $afternoon_out)) {
            $valid = false;
        }

        return $valid;
    }
    
    /**
     * Populates a new attendance record object.
     *
     * @return void
     */
    public function populate() {
        $db = Database::getInstance()->db;
        $query = "SELECT * FROM attendances 
                  WHERE DATE_FORMAT(clockin, '%Y%m%d') = DATE_FORMAT(NOW(), '%Y%m%d')
                  AND user_id = :id
                  LIMIT 1";
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $this->user->getID(), PDO::PARAM_INT);
        $statement->execute();
        if ($row = $statement->fetch()) {
            $this->id = $row['id'];
            $this->clockin = $row['clockin'];
            $this->clockout = $row['clockout'];
            $this->morningin = $row['morningin'];
            $this->morningout = $row['morningout'];
            $this->lunchin = $row['lunchin'];
            $this->lunchout = $row['lunchout'];
            $this->afternoonin = $row['afternoonin'];
            $this->afternoonout = $row['afternoonout'];
            $this->partial_day = $row['partial_day'];
            $this->absent_reason_id = $row['absent_reason_id'];
            $this->intended_date = $row['intended_date'];
            $this->updated_at = $row['updated_at'];
            $this->created_at = $row['created_at'];
            $this->user_id = $row['user_id'];
            $this->pto = $row['pto'];
            $this->non_paid_in = $row['non_paid_in'];
            $this->non_paid_out = $row['non_paid_out'];
            $this->arrive_late = $row['arrive_late'];
            $this->leave_early = $row['leave_early'];
            $this->arrive_early = $row['arrive_early'];
        }
    }

    /**
     * Populates the instance's properties with the specified date's data from the db. 
     * Can be used to populate records.
     *
     * @return void
    */
    public function populateByDate($date) {
        $date_string = $date->format('Y-m-d');
        $db = Database::getInstance()->db;
        $query = "SELECT * FROM attendances 
                  WHERE DATE_FORMAT(intended_date, '%Y%m%d') = DATE_FORMAT(:date, '%Y%m%d')
                  AND user_id = :id
                  LIMIT 1";
        $statement = $db->prepare($query);
        $statement->bindValue(':date', $date_string);
        $statement->bindValue(':id', $this->user->getID());
        $statement->execute();
        if ($row = $statement->fetch()) {
            $this->id = $row['id'];
            $this->clockin = $row['clockin'];
            $this->clockout = $row['clockout'];
            $this->morningin = $row['morningin'];
            $this->morningout = $row['morningout'];
            $this->lunchin = $row['lunchin'];
            $this->lunchout = $row['lunchout'];
            $this->afternoonin = $row['afternoonin'];
            $this->afternoonout = $row['afternoonout'];
            $this->intended_date = $row['intended_date'];
            $this->updated_at = $row['updated_at'];
            $this->created_at = $row['created_at'];
            $this->user_id = $row['user_id'];
            $this->absent_reason_id = $row['absent_reason_id'];
            $this->partial_day = $row['partial_day'];
            $this->pto = $row['pto'];
            $this->non_paid_in = $row['non_paid_in'];
            $this->non_paid_out = $row['non_paid_out'];
            $this->arrive_late = $row['arrive_late'];
            $this->leave_early = $row['leave_early'];
            $this->arrive_early = $row['arrive_early'];
        } else {
            return null;
        }
    }

    /**
     * Updates the selected clock-in type in the database for the specified user.
     *
     * @param  String $clockin_type The type of clock-in.
     * @return void
     */
    public function update($clockin_type) {

        // Check if record exists for the current date. If not, create it.
        if (self::checkAttendance($this->user)) {
            $db = Database::getInstance()->db;
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $query = "UPDATE attendances SET {$clockin_type} = NOW() WHERE DATE_FORMAT(intended_date, '%M %d %Y') = DATE_FORMAT(NOW(), '%M %d %Y') AND user_id = :user_id LIMIT 1";
            $statement = $db->prepare($query);
            $statement->bindValue(':user_id', $this->user->getID());
            $statement->execute();
            $this->user->updateBuildingStatus($clockin_type);
        } else {
            try {
                $this->createAttendance();
            } catch (PDOException $e) {
                print "Error: " . $e->getMessage();
            }
            
            $this->user->updateBuildingStatus($clockin_type);
        }
    }
    
    /**
     * Gets the attendance report of the specified date.
     *
     * @param  String $date: The specified day in Year, Month, Day format.
     * @return array
     */
    public static function getDayReport($date, $guest=false) {
        $user_type_range = ($guest) ? 3 : 2;
        $array = [];
        $db = Database::getInstance()->db;
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query = "SELECT users.id, first_name, user_type_id, last_name, clockin, clockout, morningin, morningout, lunchin, lunchout, afternoonin, afternoonout, absent_reason_id, attendances.created_at 
                  FROM users LEFT JOIN attendances 
                  ON users.id = attendances.user_id 
                  AND DATE_FORMAT(attendances.clockin, '%Y %m %d') = DATE_FORMAT(". "'". $date ."'" .", '%Y %m %d') 
                  ORDER BY users.last_name";
        $statement = $db->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch()) {
            // Do not include users of type 'guest'
            if ($row['user_type_id'] != 3) {
                $temp = array('id' => $row['id'], 'first_name' => $row['first_name'], 'last_name' => $row['last_name'], 'clock_in' => $row['clockin'], 'clock_out' => $row['clockout'], 'morning_in' => $row['morningin'],
                            'morning_out' => $row['morningout'], 'lunch_in' => $row['lunchin'], 'lunch_out' => $row['lunchout'], 'afternoon_in' => $row['afternoonin'], 'afternoon_out' => $row['afternoonout'],
                            'date' => $row['created_at']);
                array_push($array, $temp);
            } 
        }

        return $array;
    }

    public static function getGuestReport($date) {
        $date = "'" . $date . "'";
        //$db = Database::getInstance()->db;
        Database::query("SELECT first_name, last_name, user_type_id, clockin, clockout, morningin, morningout, 
                                lunchin, lunchout, afternoonin, afternoonout, `attendances`.created_at
                         FROM `users` JOIN `attendances`
                         ON `users`.id = `attendances`.user_id
                         WHERE user_type_id = 3
                         AND DATE_FORMAT(`attendances`.created_at, '%Y %m %d') = DATE_FORMAT({$date}, '%Y %m %d')");
        //$db->execute();
        return Database::result_set();
    }

    public static function getWeeklyReport($from, $to) {
        $array = [];
        $dates_between_array = [];
        $db = Database::getInstance()->db;

        // This returns a set of days between the $from date and the $to date inclusive.
        $dates_between_query = "select * from (select adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date from (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0, (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1, (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2, (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3, (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v where selected_date between " . "'" . $from . "'" . " and " . "'" . $to  ."'" . " order by selected_date asc";
        $dates_between_statement = $db->prepare($dates_between_query);
        $dates_between_statement->execute();

        // Iterate through the result set and append days to an array.
        while ($row = $dates_between_statement->fetch()) {
            array_push($dates_between_array, $row['selected_date']);
        }

        // Iterate through the array with the dates, then get the daily report array
        // for that date and append it into a new array.
        foreach ($dates_between_array as $date) {
            array_push($array, ["{$date}", Attendance::getDayReport($date)]);
        }
        
        return $array;
    }
    
    /**
     * Gets a list of dates from the range of two dates.
     *
     * @param  String $range: Format = 'YYYY-MM-DD to YYYY-MM-DD'
     * @return void
     */
    public static function getDatesFromRange($range) {
        $array = [];
        $db = Database::getInstance()->db;
        $to = trim(substr($range, strpos($range, ' ', strpos($range, ' ') + 1)));
        $from = trim(substr($range, 0, strpos($range, ' ')));

        // This returns a set of days between the $from date and the $to date inclusive.
        $dates_between_query = "select * from (select adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date from (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0, (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1, (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2, (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3, (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v where selected_date between " . "'" . $from . "'" . " and " . "'" . $to  ."'" . " order by selected_date asc";
        $dates_between_statement = $db->prepare($dates_between_query);
        $dates_between_statement->execute();

        // Iterate through the result set and append days to an array.
        while ($row = $dates_between_statement->fetch()) {
            array_push($array, $row['selected_date']);
        }

        return $array;
    }

    public static function getDayOfWeek($date) {
        $date = "'" . $date . "'";
        $db = Database::getInstance()->db;
        $sql = "SELECT DAYOFWEEK({$date}) as day";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $day_value = $stmt->fetch()['day'];

        $day = 'Sunday';

        switch ($day_value) {
            case 2:
                $day = 'Monday';
            break;
            case 3:
                $day = 'Tuesday';
            break;
            case 4:
                $day = 'Wednesday';
            break;
            case 5:
                $day = 'Thursday';
            break;
            case 6:
                $day = 'Friday';
            break;
            case 7:
                $day = 'Saturday';
            break;
        }

        return $day;
    }

    public static function getAttendanceByWeekDay($date) {
        $day = self::getDayOfWeek($date, 'day');
        $db = Database::getInstance()->db;
        $sql = "SELECT users.id, username, first_name, last_name, clockin, clockout, morningin, morningout, lunchin, lunchout, afternoonin, afternoonout, user_id, attendances.created_at
                FROM users LEFT JOIN attendances ON users.id = user_id
                AND DATE_FORMAT(attendances.clockin, '%y%m%d') = DATE_FORMAT({$date}, '%y%m%d')";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $array = array();

        while ($row = $stmt->fetch()) {
            array_push($array, [$day, $row['username'], $row['clockin']]);
        }

        return $array;
    }

    // Try adding isLate function
    public static function getUserAttendance($user, $week) {
        $to = "'" . trim(substr($week, strpos($week, ' ', strpos($week, ' ') + 1))) . "'";
        $from = "'" . trim(substr($week, 0, strpos($week, ' '))) . "'";
        //$html_check = "<p class='m-0 text-center font-weight-bold' ><box-icon size='36px' name='check' color='#0eb80b' ></box-icon></p>";
        $html_check = "<p class='m-0 text-center font-weight-bold' ><i class='fa fa-check' style='color:#0eb80b;font-size:20px;' aria-hidden='true'></i></p>";
        $db = Database::getInstance()->db;
        $user_id = $user->getID();
        $sql = "SELECT `username`, `first_name`, `last_name`, `attendances`.`clockin`, `absent_reason_id`, `partial_day`
                FROM `users` JOIN `attendances` 
                ON `users`.`id` = `user_id` 
                WHERE `user_id` = {$user_id} 
                AND `attendances`.`clockin` 
                BETWEEN DATE_FORMAT({$from}, '%y%m%d') AND DATE_FORMAT({$to}, '%y%m%d')";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $array = [];
        $array[$user->getUsername()]['Sunday'] = '';
        $array[$user->getUsername()]['Monday'] = '';
        $array[$user->getUsername()]['Tuesday'] = '';
        $array[$user->getUsername()]['Wednesday'] = '';
        $array[$user->getUsername()]['Thursday'] = '';
        $array[$user->getUsername()]['Friday'] = '';
        $array[$user->getUsername()]['Saturday'] = '';
        $array[$user->getUsername()]['username'] = $user->getUsername();

        while ($row = $stmt->fetch()) {
            $date = $row['clockin'];
            
            $formated_date = "";
            if ($row['partial_day']) {
                $formated_date = "<p class='m-0 text-center text-secondary font-weight-bold'>Away<br />" . '<small>' . $row['partial_day'] . '</small>' . "</p>";
            } else if ($row['absent_reason_id']) {
                $formated_date = "<p class='m-0 text-center text-secondary font-weight-bold'>" . AbsentReason::findByID($row['absent_reason_id'])->getReason() . "</p>";
            } else if ((date('H:i', strtotime($date)) >= '08:31') && (date('H:i', strtotime($date)) <= '23:59')) {
                $formated_date = "<p class='m-0 text-center text-danger font-weight-bold' style='font-size:16px;'>" . date('H:i', strtotime($date)) . "</p>";
            } else {
                $formated_date = "<p class='m-0 text-center text-success font-weight-bold' style='font-size:16px;'>" . date('H:i', strtotime($date)) . "</p>";
            }

            if (trim(Attendance::getDayOfWeek($date, 'day')) == 'Sunday') {
                $array[$user->getUsername()]['Sunday'] = $formated_date;
            } else if (trim(Attendance::getDayOfWeek($date, 'day')) == 'Monday') {
                $array[$user->getUsername()]['Monday'] = $formated_date;
            } else if (trim(Attendance::getDayOfWeek($date, 'day')) == 'Tuesday') {
                $array[$user->getUsername()]['Tuesday'] = $formated_date;
            } else if (trim(Attendance::getDayOfWeek($date, 'day')) == 'Wednesday') {
                $array[$user->getUsername()]['Wednesday'] = $formated_date;
            } else if (Attendance::getDayOfWeek($date, 'day') == 'Thursday') {
                $array[$user->getUsername()]['Thursday'] = $formated_date;
            } else if (Attendance::getDayOfWeek($date, 'day') == 'Friday') {
                $array[$user->getUsername()]['Friday'] = $formated_date;
            } else if (Attendance::getDayOfWeek($date, 'day') == 'Saturday') {
                $array[$user->getUsername()]['Saturday'] = $formated_date;
            }
        }

        return $array;
    }
    
    /**
     * Returns records of days a user was late.
     *
     * @param  mixed $user
     * @return void
     */
    public static function getTwoWeekLateReport($user) {
        $array = [];
        $db = Database::getInstance()->db;
        $sql = "SELECT `username`, `attendances`.* FROM `users` JOIN `attendances` 
                ON `users`.`id` = `attendances`.`user_id` 
                AND TIME_FORMAT(`attendances`.`clockin`, '%H:%i') 
                BETWEEN TIME_FORMAT('08:31', '%H:%i') AND TIME_FORMAT('16:30', '%H:%i') 
                AND DATE_SUB(NOW(), INTERVAL 2 WEEK)
                AND `user_id` = {$user->getID()}
                AND user_type_id <> 3
                ORDER BY clockin asc";
        $stmt = $db->prepare($sql);
        $stmt->execute();


        $today_date = new DateTime();
        $two_week_date = new DateTime();
        $two_week_date->sub(new DateInterval('P6D'));
        $date_range = $two_week_date->format('Y-m-d') . ' to ' . $today_date->format('Y-m-d');
        $days_array = Attendance::getDatesFromRange($date_range);
        $is_absent = [true, true, true, true, true, true, true];
        $array = [];

        $array[$user->getUsername()][$days_array[0]] = '';
        $array[$user->getUsername()][$days_array[1]] = '';
        $array[$user->getUsername()][$days_array[2]] = '';
        $array[$user->getUsername()][$days_array[3]] = '';
        $array[$user->getUsername()][$days_array[4]] = '';
        $array[$user->getUsername()][$days_array[5]] = '';
        $array[$user->getUsername()][$days_array[6]] = '';
        $array[$user->getUsername()]['username'] = $user->getUsername();

        while ($row = $stmt->fetch()) {
            $date = $row['clockin'];
            $day = self::getDayOfWeek($date);
            switch (date('Y-m-d', strtotime($date))) {
                case $days_array[0]:
                    $array[$user->getUsername()][$days_array[0]] = $date;
                    $is_absent[0] = false;
                break;
                case $days_array[1]:
                    $array[$user->getUsername()][$days_array[1]] = $date;
                    $is_absent[1] = false;
                break;
                case $days_array[2]:
                    $array[$user->getUsername()][$days_array[2]] = $date;
                    $is_absent[2] = false;
                break;
                case $days_array[3]:
                    $array[$user->getUsername()][$days_array[3]] = $date;
                    $is_absent[3] = false;
                break;
                case $days_array[4]:
                    $array[$user->getUsername()][$days_array[4]] = $date;
                    $is_absent[4] = false;
                break;
                case $days_array[5]:
                    $array[$user->getUsername()][$days_array[5]] = $date;
                    $is_absent[5] = false;
                break;
                case $days_array[6]:
                    $array[$user->getUsername()][$days_array[6]] = $date;
                    $is_absent[6] = false;
                break;
            }
        }
        return $array;
    }

    public static function getWeekAttendanceRange($date, $user) {
        $attendances = [];
        Database::query("SELECT *
                         FROM attendances 
                         WHERE DATE_FORMAT(clockin, '%Y%m%d') BETWEEN DATE_FORMAT(DATE_SUB('$date', INTERVAL 1 WEEK), '%Y%m%d')
                         AND DATE_FORMAT('$date', '%Y%m%d') 
                         AND user_id = :id");
        Database::bind(':id', $user->getID(), PDO::PARAM_INT);
        // $stmt = Database::getStatement();
        foreach (Database::result_set() as $key => $value) {
            
            array_push($attendances, self::createWithUserRow($user, $value));
        }

        return $attendances;
    }

    public static function areMonthsEqual($from, $to) {
        return $from == $to;
    }
    
    /**
     * getDailyAttendance
     *
     * @param  mixed $date
     * @return void
     */
    private static function getDailyAttendance($date) {
        $attendance_array = array();
        $date = "'" . $date . "'";
        $db = Database::getInstance()->db;
        $sql = "SELECT users.id, username, first_name, last_name, clockin, clockout, morningin, morningout, lunchin, lunchout, afternoonin, afternoonout, absent_reason_id, user_id, attendances.created_at
                FROM users LEFT JOIN attendances ON users.id = user_id
                AND DATE_FORMAT(attendances.created_at, '%y%m%d') = DATE_FORMAT({$date}, '%y%m%d')";
        
        $stmt = $db->prepare($sql);
        $stmt->execute();

        while ($row = $stmt->fetch()) {
            $user = User::findByID($row['id']);
            $attendance = self::createWithUserRow($user, $row);

            array_push($attendance_array, $attendance);
        }

        return $attendance_array;
    }

    private function fill($row) {
        $this->id = $row['id'];
        $this->clockin = $row['clockin'];
        $this->clockout = $row['clockout'];
        $this->morningin = $row['morningin'];
        $this->morningout = $row['morningout'];
        $this->lunchin = $row['lunchin'];
        $this->lunchout = $row['lunchout'];
        $this->afternoonin = $row['afternoonin'];
        $this->afternoonout = $row['afternoonout'];
        $this->absent_reason_id = $row['absent_reason_id'];
        $this->partial_day = $row['partial_day'];
        $this->intended_date = $row['intended_date'];
        $this->updated_at = $row['updated_at'];
        $this->created_at = $row['created_at'];
        $this->user_id = $row['user_id'];
        $this->pto = $row['pto'];
        $this->non_paid_in = $row['non_paid_in'];
        $this->non_paid_out = $row['non_paid_out'];
        $this->arrive_late = $row['arrive_late'];
        $this->leave_early = $row['leave_early'];
        $this->arrive_early = $row['arrive_early'];
    }
 
    /**
     * Checks whether there is an existing record for the user on the current date.
     * 
     * @return boolean
     */
    public static function checkAttendance($user) {
        $isValid = false;
        $db = Database::getInstance()->db;
        $query = "SELECT * FROM attendances 
                  WHERE DATE_FORMAT(intended_date, '%Y %m %d') = DATE_FORMAT(NOW(), '%Y %m %d') AND user_id = :user_id LIMIT 1;";
        $statement = $db->prepare($query);
        $statement->bindValue(':user_id', $user->getID());

        if ($statement->execute()) {
            if ($statement->rowCount() > 0) {
                $isValid = true;
            }
        }

        return $isValid;
    }

    public static function insert($intended_date, $user) {
        Database::query("INSERT INTO attendances (intended_date, user_id) VALUES (:intended_date, :user_id)");
        Database::bind(':intended_date', $intended_date, PDO::PARAM_STR);
        Database::bind(':user_id', $user->getID(), PDO::PARAM_INT);
        Database::execute();
        return Attendance::getLast();
    }

    public function updatePartialDay($partial_day) {
        $partial_day = ($partial_day == '') ? null : $partial_day;
        Database::query("UPDATE attendances SET partial_day = :partial_day WHERE id = :id");
        Database::bind(':partial_day', $partial_day, PDO::PARAM_STR);
        Database::bind(':id', $this->id, PDO::PARAM_INT);
        Database::execute();
    }

    public function updateAbsentReasonID($reason_id)  {
        $reason_id = ($reason_id == '') ? null : $reason_id;
        Database::query("UPDATE attendances SET absent_reason_id = :absent_reason_id WHERE id = :id");
        Database::bind(':absent_reason_id', $reason_id, PDO::PARAM_INT);
        Database::bind(':id', $this->id, PDO::PARAM_INT);
        Database::execute();
    }

    public function updateClockIn($clock_in) {
        $clock_in = ($clock_in == '') ? null : $clock_in;
        Database::query("UPDATE attendances SET clockin = :clockin WHERE id = :id");
        Database::bind(':clockin', $clock_in, PDO::PARAM_STR);
        Database::bind(':id', $this->id, PDO::PARAM_INT);
        Database::execute();
    }

    public function updatePTO($pto) {
        Database::query("UPDATE attendances SET pto = :pto WHERE id = :id");
        Database::bind(':pto', $pto, PDO::PARAM_STR);
        Database::bind(':id', $this->id, PDO::PARAM_INT);
        Database::execute();
    }
    
    /**
     * Creates a new record in the attendances table when a user clocks in for the first time of the day.
     *
     * @return void
     */
    private function createAttendance() {
        $db = Database::getInstance()->db;
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query = "INSERT INTO attendances (clockin, intended_date, user_id) VALUES (NOW(), NOW(), :user_id)";
        $statement = $db->prepare($query);
        $date = new DateTime();
        //$statement->bindValue(':clockin', $date->format('Y-m-d H:i:s'));
        $statement->bindValue(':user_id', $this->user->getID(), PDO::PARAM_INT);
        $statement->execute();
    }
    
    /**
     * Sets the respective clock-in type to reflect changes made in the database.
     *
     * @param  String $clockin_type: The type of clock-in selected by the user.
     * @return void
     */
    private function setClockIn($clockin_type) {

        $date = new DateTime();
        $date_string = $date->format('Y-m-d H:i:s');

        switch ($clockin_type) {
            case 'clockin':
                $this->clockin = $date_string;
            break;
            case 'clockout':
                $this->clockout = $date_string;
            break;
            case 'morningin':
                $this->morningin = $date_string;
            break;
            case 'morningout':
                $this->morningout = $date_string;
            break;
            case 'lunchin':
                $this->lunchin = $date_string;
            break;
            case 'lunchout':
                $this->lunchout = $date_string;
            break;
            case 'afternoonin':
                $this->afternoonin = $date_string;
            break;
            case 'afternoonout':
                $this->afternoonout = $date_string;
            break;
        }
    }


    // For migrating database column intended_date.
    public static function createWithRow($row) {
        $instance = new self();
        $instance->fill($row);
        $instance->user = User::findByID($instance->user_id);
        return $instance;
    }

    public static function getAllAttendances() {
        Database::query("SELECT * FROM attendances");
        $stmt = Database::getStatement();
        $attendances = [];

        while ($row = $stmt->fetch()) {
            array_push($attendances, self::createWithRow($row));
        }

        return $attendances;
    }

    public static function getAllGuestRecordsByDate($date) {
        $records = [];
        Database::query("SELECT * FROM attendances JOIN users 
                         ON user_id = users.id 
                         WHERE user_type_id = :user_type_id
                         AND DATE_FORMAT(intended_date, '%Y-%m-%d') = DATE_FORMAT(:intended_date, '%Y-%m-%d')");
        Database::bind(':user_type_id', 3, PDO::PARAM_INT);
        Database::bind(':intended_date', $date->format('Y-m-d'), PDO::PARAM_STR);
        $stmt = Database::getStatement();
        while ($row = $stmt->fetch()) {
            array_push($records, self::createWithRow($row));
        }

        return $records;
    }

    public function updateIntendedDate($date) {
        Database::query("UPDATE attendances SET intended_date = DATE_FORMAT(:intended_date, '%Y-%m-%d') WHERE id = :id");
        Database::bind(':intended_date', $date, PDO::PARAM_STR);
        Database::bind(':id', $this->id, PDO::PARAM_INT);
        Database::execute();
    }

    public function updateNonPaidIn($non_paid_in) {
        $non_paid_in = ($non_paid_in == '') ? null : $non_paid_in;
        Database::query("UPDATE attendances SET non_paid_in = :non_paid_in WHERE id = :id");
        Database::bind(':non_paid_in', $non_paid_in, PDO::PARAM_STR);
        Database::bind(':id', $this->id, PDO::PARAM_INT);
        Database::execute();
    }

    public function updateNonPaidOut($non_paid_out) {
        $non_paid_out = ($non_paid_out == '') ? null : $non_paid_out;
        Database::query("UPDATE attendances SET non_paid_out = :non_paid_out WHERE id = :id");
        Database::bind(':non_paid_out', $non_paid_out, PDO::PARAM_STR);
        Database::bind(':id', $this->id, PDO::PARAM_INT);
        Database::execute();
    }

    public function updateArriveLate($arrive_late) {
        $arrive_late = ($arrive_late == '') ? null : $arrive_late;
        Database::query("UPDATE attendances SET arrive_late = :arrive_late WHERE id = :id");
        Database::bind(':arrive_late', $arrive_late, PDO::PARAM_STR);
        Database::bind(':id', $this->id, PDO::PARAM_INT);
        Database::execute();
    }

    public function updateLeaveEarly($leave_early) {
        $leave_early = ($leave_early == '') ? null : $leave_early;
        Database::query("UPDATE attendances SET leave_early = :leave_early WHERE id = :id");
        Database::bind(':leave_early', $leave_early, PDO::PARAM_STR);
        Database::bind(':id', $this->id, PDO::PARAM_INT);
        Database::execute();
    }

    public function updateArriveEarly($arrive_early) {
        $arrive_early = ($arrive_early == '') ? null : $arrive_early;
        Database::query("UPDATE attendances SET arrive_early = :arrive_early WHERE id = :id");
        Database::bind(':arrive_early', $arrive_early, PDO::PARAM_STR);
        Database::bind(':id', $this->id, PDO::PARAM_INT);
        Database::execute();
    }

    public static function getSickDays() {
        Database::query("SELECT * FROM attendances WHERE absent_reason_id IS NOT NULL");
        $stmt = Database::getStatement();
        $attendances = [];

        while ($row = $stmt->fetch()) {
            array_push($attendances, self::createWithRow($row));
        }

        return $attendances;
    }

    public function getAbsentReason() {
        return AbsentReason::findByID($this->getAbsentReasonID())->getReason();
    }
}
?>
