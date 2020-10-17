<?php
require_once('classes.php');

/**
 * UserPosition
 */
class UserPosition {
    private $id;
    private $position;
    private static $error;

    public function getID() { return $this->id; }
    public function getPosition() { return $this->position; }

    private function __construct() { }

    public static function createWithRow($row) {
        $instance = new self();
        $instance->fill($row);
        return $instance;
    }

    public static function findByID($id) {
        Database::query("SELECT * FROM user_positions WHERE id = :id");
        Database::bind(':id', $id, PDO::PARAM_INT);
        return self::createWithRow(Database::getRow());
    }

    public static function getAllPositions() {
        Database::query("SELECT * FROM user_positions");
        $stmt = Database::getStatement();
        $positions = [];

        while ($row = $stmt->fetch()) {
            array_push($positions, self::createWithRow($row));
        }

        return $positions;
    }
    
    /**
     * Checks if there are any users belonging to this user position.
     *
     * @return void
     */
    public function check() {
        $result = false;

        Database::query("SELECT * FROM users WHERE user_position_id = :id LIMIT 1");
        Database::bind(":id", $this->id, PDO::PARAM_INT);
        $stmt = Database::getStatement();

        if ($stmt->rowCount() > 0) {
            $result = true;
        }

        return $result;
    }

    public static function addPosition($position) {
        Database::query("INSERT INTO user_positions (position) VALUES (:position)");
        Database::bind(':position', $position, PDO::PARAM_STR);
        Database::execute();
    }

    public function destroy() {
        Database::query("DELETE FROM user_positions WHERE id = :id");
        Database::bind(':id', $this->id, PDO::PARAM_INT);
        Database::execute();
    }

    public static function last() {
        Database::query("SELECT * FROM user_positions ORDER BY id DESC LIMIT 1");
        return self::createWithRow(Database::getRow());
    }

    private function fill($row) {
        $this->id = $row['id'];
        $this->position = $row['position'];
    }
}
