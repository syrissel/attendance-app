<?php
// Get get absolute path of current directory.
$root_path = __DIR__ . '/..';
require($root_path . '/vendor/autoload.php');

/**
 * Database: Singleton PDO wrapper. Allows for no more than one instance to be in memory.
 * 
 * @author:  Steph Mireault
 * @date:    July 22, 2020
 */
class Database {
    public $db;
    private $statement;
    private static $instance;

    private function __construct() {
        try {
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
            $dotenv->load();
            define('DB_DSN',"mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8");
            define('DB_USER', $_ENV['DB_USER']);
            define('DB_PASS', $_ENV['DB_PASS']);   
            $this->db = new PDO(DB_DSN, DB_USER, DB_PASS);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            print "Error: " . $e->getMessage();
            die();
        }
    }

    public static function getInstance() {
        if (!isset(self::$instance)) {
            $object = __CLASS__;
            self::$instance = new $object;
        }
        return self::$instance;
    }
    
    /**
     * Prepares an SQL query.
     *
     * @param  String $query
     * @return void
     */
    public static function query($query) {
        $instance = self::getInstance();
        $instance->statement = $instance->db->prepare($query);
    }
    
    /**
     * Binds a value to a parameter corresponding to a query.
     *
     * @param  mixed $param: Database attribute.
     * @param  mixed $value: Value to be bound to the attribute.
     * @param  mixed $type:  The database datatype.
     * @return void
     */
    public static function bind($param, $value, $type = null) {
        $instance = self::getInstance();

        // If a type was not specified, check what type the value is.
        if (is_null($type)) {
            if (is_int($value)) {
                $type = PDO::PARAM_INT;
            } else if (is_bool($value)) {
                $type = PDO::PARAM_BOOL;
            } else if (is_null($value)) {
                $type = PDO::PARAM_NULL;
            } else {
                $type = PDO::PARAM_STR;
            }
        }

        $instance->statement->bindValue($param, $value, $type);
    }
    
    /**
     * Executes PDO statement.
     *
     * @return void
     */
    public static function execute() {
        $instance = self::getInstance();
        return $instance->statement->execute();
    }

    public static function result_set() {
        $instance = self::getInstance();
        $instance->execute();
        return $instance->statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getRow() {
        $instance = self::getInstance();
        $instance->execute();
        return $instance->statement->fetch();
    }

    public static function getStatement() {
        $instance = self::getInstance();
        $instance->execute();
        return $instance->statement;
    }

    public static function close() {
        $instance = self::getInstance();
        $instance->db = null;
    }
}