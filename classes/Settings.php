<?php
require_once('classes.php');

/**
 * Settings: Class representing table in database. Only has one row at all times.
 * 
 * @author:  Steph Mireault
 * @date:    July 22, 2020
 */
class Settings {
    private $site_logo;
    private $clock_out_time;
    private $notice_content;
    private $service_team_phone;
    private $service_team_toll_free;
    private static $instance;

    public function getSiteLogo() { return $this->site_logo; }
    public function getClockOutTime() { return $this->clock_out_time; }
    public function getNoticeContent() { return $this->notice_content; }
    public function getServiceTeamPhone() { return $this->service_team_phone; }
    public function getServiceTeamTollFree() { return $this->service_team_toll_free; }

    private function __construct() {
        $this->populate();
    }

    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function update($site_logo) {
        Database::query("UPDATE settings SET site_logo = :site_logo");
        Database::bind(':site_logo', $site_logo, PDO::PARAM_STR);
        $this->site_logo = $site_logo;
        Database::execute();
        //Database::close();
    }

    public function updateClockOutTime($clock_out_time) {
        Database::query("UPDATE settings SET clock_out_time = :clock_out_time");
        Database::bind(':clock_out_time', $clock_out_time, PDO::PARAM_STR);
        $this->clock_out_time = $clock_out_time;
        Database::execute();
    }

    public function updateNoticeContent($content) {
        Database::query("UPDATE settings SET notice_content = :notice_content");
        Database::bind(":notice_content", $content, PDO::PARAM_STR);
        $this->notice_content = $content;
        Database::execute();
    }

    public function updatePhone($phone) {
        Database::query("UPDATE settings SET service_team_phone = :service_team_phone");
        Database::bind(':service_team_phone', $phone, PDO::PARAM_STR);
        $this->service_team_phone = $phone;
        Database::execute();
    }

    public function updateTollFree($toll_free) {
        Database::query("UPDATE settings SET service_team_toll_free = :service_team_toll_free");
        Database::bind(':service_team_toll_free', $toll_free, PDO::PARAM_STR);
        $this->service_team_toll_free = $toll_free;
        Database::execute();
    }

    private static function insert() {
        Database::query("INSERT INTO settings (site_logo) VALUES ('Clock App')");
        Database::execute();
        //Database::close();
    }

    private static function exists() {
        $db = Database::getInstance()->db;
        $sql = "SELECT * FROM settings LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        return ($stmt->rowCount() > 0);
    }

    private function populate() {

        if (self::exists()) {
            Database::query("SELECT * FROM settings LIMIT 1");
            $result = Database::result_set();
            $this->site_logo = $result[0]['site_logo'];
            $this->clock_out_time = $result[0]['clock_out_time'];
            $this->notice_content = $result[0]['notice_content'];
            $this->service_team_phone = $result[0]['service_team_phone'];
            $this->service_team_toll_free = $result[0]['service_team_toll_free'];
        } else {
            self::insert();
            $this->site_logo = 'Clock App';
        }
    }
}