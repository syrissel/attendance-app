<?php 
require_once('classes.php');

/**
 * UserType: Class representing UserType table in database.
 * 
 * @author:  Steph Mireault
 * @date:    July 22, 2020
 */
class UserType {
    private $id;
    private $type;
    private $user;

    public function getID() { return $this->id; }
    public function getType() { return $this->type; }

    private function __construct() { }

    public static function createWithRow($row) {
        $instance = new self();
        $instance->fill($row);
        return $instance;
    }

    public static function findByID($id) {
        Database::query("SELECT * FROM user_types WHERE id = :id LIMIT 1");
        Database::bind(':id', $id, PDO::PARAM_INT);
        $row = Database::getRow();
        return self::createWithRow($row);
    }

    public static function findByType($user_type) {
        Database::query("SELECT * FROM user_types WHERE `type` = :type LIMIT 1");
        Database::bind(':type', $user_type, PDO::PARAM_STR);
        $row = Database::getRow();
        return self::createWithRow($row);
    }

    private function fill($row) {
        $this->id =   $row['id'];
        $this->type = $row['type'];
    }
    
    /**
     * Gets all user types from database.
     *
     * @param  bool $guest: True to include guest type, false to exclude.
     * @return Array:       Collection of UserTypes.
     */
    public static function getAllTypes($guest=true) {
        $guest = ($guest) ? '' : "WHERE id <> 3";
        Database::query("SELECT id, type FROM user_types {$guest}");
        
        $types = [];

        foreach (Database::result_set() as $key => $value) {
            array_push($types, UserType::createWithRow($value));
        }

        return $types;
    }

    private static function fetchType($id) {
        $type = '';
        $db = Database::getInstance()->db;
        //$query = "SELECT user_types.id, user_types.type FROM users JOIN user_types ON users.user_type_id = user_types.id WHERE users.id = :user_id";
        $query = "SELECT type FROM user_types WHERE id = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id);
        $statement->execute();
        if ($row = $statement->fetch()) {
            $type = $row['type'];
        }

        return $type;
    }

    public function __toString() {
        return strval($this->type);
    }
}
?>