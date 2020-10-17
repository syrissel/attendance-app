<?php
require_once('classes.php');

class AbsentReason {
    private $id;
    private $reason;

    public function getID()     { return $this->id; }
    public function getReason() { return $this->reason; }

    private function __construct() { }

    public static function createWithRow($row) {
        $instance = new self();
        $instance->fill($row);
        return $instance;
    }

    public static function findByID($id) {
        Database::query("SELECT * FROM absent_reasons WHERE id = :id LIMIT 1");
        Database::bind(':id', $id, PDO::PARAM_INT);
        return self::createWithRow(Database::getRow());
    }

    public static function getAllReasons() {
        Database::query("SELECT * FROM `absent_reasons`");
        $stmt = Database::getStatement();
        $reasons = [];

        while ($row = $stmt->fetch()) {
            array_push($reasons, self::createWithRow($row));
        }

        return $reasons;
    }

    public static function addReason($reason) {
        Database::query("INSERT INTO absent_reasons (reason) VALUES (:reason)");
        Database::bind(':reason', $reason, PDO::PARAM_STR);
        Database::execute();
    }

    public function check() {
        $result = false;

        Database::query("SELECT * FROM attendances WHERE absent_reason_id = :id LIMIT 1");
        Database::bind(":id", $this->id, PDO::PARAM_INT);
        $stmt = Database::getStatement();

        if ($stmt->rowCount() > 0) {
            $result = true;
        }

        return $result;
    }

    public function destroy() {
        Database::query("DELETE FROM absent_reasons WHERE id = :id");
        Database::bind(':id', $this->id, PDO::PARAM_INT);
        Database::execute();
    }

    private function fill($row) {
        $this->id = $row['id'];
        $this->reason = $row['reason'];
    }
}