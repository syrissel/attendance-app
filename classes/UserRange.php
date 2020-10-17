<?php
require_once('classes.php');

/**
 * Class containing a collection of users for one date.
 */
class UserRange {
    private $date;
    private $users;

    public function getUsers() { return $this->users; }
    public function getDate() { return $this->date; }

    private function __construct() { }

    public static function create($users, $date) {
        $instance = new self();
        $instance->users = $users;
        $instance->date = $date;
        return $instance;
    }

    public function checkRecords() {
        return count(Attendance::getAllGuestRecordsByDate($this->date)) > 0;
    }
}