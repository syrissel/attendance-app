<?php
require_once('classes.php');

class AttendanceRange {
    private $dates;
    private $user;
    private $attendances;
    private $error;

    public function getUser() { return $this->user; }
    public function getAttendances() { return $this->attendances; }
    public function getDates() { return $this->dates; }
    public function getError() { return $this->error; }

    private function __construct() { }

    public function getTotalMinutes() {
        $minutes = 0;

        try {
            foreach ($this->attendances as $a) {
                // $pto_minutes = $a->getPTO() * 60;
                $minutes += abs(getTimeDiffInMinutes($a->getRoundedClockIn(), $a->getRoundedClockOut()));
                $minutes -= abs($a->getTotalNonPaidMinutes());
            }
        } catch (AttendanceException $e) {
            $this->error = $e->getMessage();
            return false;
        }


        return $minutes;
    }

    public function getTotalGrossMinutes() {
        $minutes = 0;

        try {
            foreach ($this->attendances as $a) {
                // $pto_minutes = $a->getPTO() * 60;
                $minutes += abs(getTimeDiffInMinutes($a->getRoundedClockIn(), $a->getRoundedClockOut()));
            }
        } catch (AttendanceException $e) {
            $this->error = $e->getMessage();
            return false;
        }


        return $minutes;
    }

    public function getTotalNonPaidMinutes() {
        $minutes = 0;

        foreach ($this->attendances as $a) {
            $minutes += abs($a->getTotalNonPaidMinutes());
        }
        return $minutes;
    }

    public function getTotalPTO() {
        $pto = 0;

        foreach ($this->attendances as $a) {
            $pto += $a->getPTO();
        }

        return $pto;
    }

    public function getTotalNetMinutes() {
        $minutes = 0;

        return $this->getTotalGrossMinutes() - $this->getTotalNonPaidMinutes() + $this->getTotalPTO();
    }
    
    /**
     * Get total actual estimated minutes worked in office, not including non-paid breaks or paid time off.
     *
     * @return void
     */
    public function getTotalActualEstimatedMinutes() {
        $minutes = 0;

        foreach ($this->attendances as $a) {
            $minutes = $minutes + $a->getTotalEstimatedMinutes();
        }

        return $minutes;
    }
    
    /**
     * Get total estimated minutes worked in office less non-paid breaks.
     *
     * @return void
     */
    public function getTotalEstimatedMinutes() {
        $minutes = 0;

        foreach ($this->attendances as $a) {
            $minutes = $minutes + $a->getTotalEstimatedMinutes() - $a->getTotalNonPaidMinutes();
        }

        return $minutes;
    }
    
    /**
     * Get the total estimated amount of worked minutes minus non-paid breaks plus paid time off.
     *
     * @return void
     */
    public function getTotalNetEstimatedMinutes() {
        return $this->getTotalActualEstimatedMinutes() - $this->getTotalNonPaidMinutes() + $this->getTotalPTO();
    }

    public function getEstimatedNonPaidHoursDifference() {
        return ($this->user->getExpectedWorkHours() - minutesToHours($this->getTotalNetEstimatedMinutes()));
    }

    public function getNonPaidHoursDifference() {
        return ($this->user->getExpectedWorkHours() - minutesToHours($this->getTotalNetMinutes()));
    }

    public static function create($dates, $user) {
        $instance = new self();
        $instance->dates = $dates;
        $instance->user = $user;
        $instance->attendances = [];
        foreach ($dates as $date) {
            $attendance = Attendance::createWithDate($user, $date);
            if ($attendance->checkClockOut()) {
                $instance->error = "{$user->getFullName()}";
            }
            array_push($instance->attendances, $attendance);
        }

        return $instance;
    }

    public static function addTotalMinutes($ar_array) {
        $minutes = 0;

        foreach ($ar_array as $ar) {
            $minutes += $ar->getTotalMinutes();
        }

        return $minutes;
    }

    public static function addTotalPTO($ar_array) {
        $pto = 0;

        foreach ($ar_array as $ar) {
            $pto += $ar->getTotalPTO();
        }

        return $pto;
    }

    public static function addTotalPTOAndMinutes($ar_array) {
        return self::addTotalMinutes($ar_array) + self::addTotalPTO($ar_array) - self::addTotalNonPaidMinutes($ar_array);
    }

    public static function addTotalNonPaidMinutes($ar_array) {
        $minutes = 0;

        foreach ($ar_array as $ar) {
            $minutes += $ar->getTotalNonPaidMinutes();
        }

        return $minutes;
    }

    public static function addTotalNonPaidHours($ar_array) {
        $hours = 0;

        foreach ($ar_array as $range) {
            $hours += $range->getNonPaidHoursDifference();
        }

        return $hours;
    }

    public static function addTotalEstimatedNonPaidHours($ar_array) {
        $hours = 0;

        foreach ($ar_array as $range) {
            $hours += $range->getEstimatedNonPaidHoursDifference();
        }

        return $hours;
    }
    
    /**
     * Add all actual hours worked in office less non paid break hours.
     *
     * @param  mixed $ar_array
     * @return void
     */
    public static function addTotalEstimatedMinutes($ar_array) {
        $minutes = 0;

        foreach ($ar_array as $ar) {
            $minutes = $minutes + $ar->getTotalEstimatedMinutes() - $ar->getTotalNonPaidMinutes();
        }

        return $minutes;
    }

    public static function addTotalActualEstimatedMinutes($ar_array) {
        $minutes = 0;

        foreach ($ar_array as $ar) {
            $minutes = $minutes + $ar->getTotalEstimatedMinutes();
        }

        return $minutes;
    }

    // Do not subtract unpaid minutes since it's already subtracted in getTotalEstimatedMinutes
    public static function addTotalNetEstimatedMinutes($ar_array) {
        return self::addTotalActualEstimatedMinutes($ar_array) + self::addTotalPTO($ar_array);
    }

    public static function getFullTimeRecords($ar_array, $period) {
        $interns = User::getAllInterns();
        $array = [];
        foreach ($interns as $intern) {
            array_push($array, self::create($period, $intern));
        }
        return array_diff($ar_array, $array);
    }

    public static function getInternRecords($ar_array, $period) {
        $admins = User::getAdmins();
        $array = [];
        foreach ($admins as $admin) {
            array_push($array, self::create($period, $admin));
        }
        return array_diff($ar_array, $array);
    }

    // For week range reports.
    public static function getRangesByDate($ar_array, $date) {
        $attendances = [];

        foreach ($ar_array as $ar) {
            
        }
    }

    public function __toString() {
        return $this->user->getUsername();
    }
}
?>
