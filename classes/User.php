<?php 
require_once('classes.php');
define('USER', 1);
define('ADMIN', 2);

// Custom class for user exceptions.
class UserException extends Exception { }

/**
 * User:    Class representing User table in database with standard CRUD operations.
 * 
 * @author: Steph Mireault
 * @date:   July 22, 2020
 */
class User {
    
    private $id;
    private $username;
    private $first_name;
    private $last_name;
    private $user_type_id;
    private $user_type;
    private $user_position_id;
    private $user_position;
    private $pin;
    private $building_status;
    private $total_biweekly_hours;
    private $total_biweekly_minutes;
    private $comments;
    private $overtime;
    private $organization;
    private $expected_clockin;
    private $expected_clockout;
    private $expected_work_hours;
    private $payroll_id;
    private $status;
    private $created_at;

    public function getID() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function getFirstName() { return $this->first_name; }
    public function getLastName() { return $this->last_name; }
    public function getUserType() { return $this->user_type; }
    public function getUserPosition() { return $this->user_position; }
    public function getFullName() { return $this->first_name . ' ' . $this->last_name; }
    public function getTotalBiWeeklyMinutes() { return $this->total_biweekly_minutes; }
    public function getComments() { return $this->comments; }
    public function getCreatedAt() { return $this->created_at; }
    public function getOrganization() { return $this->organization; }
    public function getOvertime() { return $this->overtime; }
    public function getStatus() { return $this->status; }
    public function getExpectedClockIn() { return $this->expected_clockin; }
    public function getExpectedClockOut() { return $this->expected_clockout; }
    public function getExpectedWorkHours() { return $this->expected_work_hours; }
    public function getPayrollID() { return $this->payroll_id; }
    public function getBuildingStatus() { return $this->building_status; }

    public function getPrettyBuildingStatus() {
        $result = '';
        $a = Attendance::createWithDate($this, new DateTime());

        if ($this->isOnBreak()) {
            $result = "<p class='text-warning font-weight-bold m-0 text-center'>Break</p>";
        } else if ($this->isIn()) {
            $result = "<p class='text-success font-weight-bold m-0 text-center'>In</p>";
        } else if ($a->getClockOut()) {
            $result = "<p class='text-danger font-weight-bold m-0 text-center'>Out for the day</p>";
        } else {
            $result = "<p class='text-secondary font-weight-bold m-0 text-center'>Absent</p>";
        }

        return $result;
    }

    public function getSimpleBuildingStatus() {
        $a = Attendance::createWithDate($this, new DateTime());
        if ($a->getAbsentReasonID()) {
            return AbsentReason::findByID($a->getAbsentReasonID())->getReason();
        } else {
        return ($this->getBuildingStatus() == 'Y') ? '<p class="m-0 text-success font-weight-bold">In</p>' : '<p class="m-0 text-danger font-weight-bold">Out</p>';

        }

    }

    public function setUsername($username) {
        $this->username = $username;
        $this->setAttribute('username', $username);
    }

    public function setLastName($last_name) {
        $this->last_name = $last_name;
        $this->setAttribute('last_name', $last_name);
    }

    public function setUserType($type) {
        $this->user_type = UserType::findByType($type);
    }

    private function setAttribute($column, $value) {
        $db = Database::getInstance()->db;
        $sql = "UPDATE users SET {$column} = :{$column} WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue("{$column}", $value, PDO::PARAM_STR);
        $stmt->bindValue('id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
    }

    private function __construct() { }

    public static function createByRow($row) {
        $instance = new self();
        $instance->fill($row);
        $instance->user_type = UserType::findByID($instance->user_type_id);
        $instance->user_position = UserPosition::findByID($instance->user_position_id);
        return $instance;
    }

    // Possibly unused.
    public static function createByAttendances($row, $date, $attendances) {
        $instance = User::createByRow($row);
        $instance->attendance_date = $date;
        $instance->attendances = $attendances;
        return $instance;
    }

    public function updateBuildingStatus($clockin_type) {
        $in_building = (strpos($clockin_type, 'out')) ? 'N' : 'Y';
        $db = Database::getInstance()->db;
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query = "UPDATE users SET in_building = :in_building WHERE id = :id LIMIT 1";
        $statement = $db->prepare($query);
        $statement->bindValue(':in_building', $in_building, PDO::PARAM_STR);
        $statement->bindValue(':id', $this->id, PDO::PARAM_INT);
        $statement->execute();
    }
    
    /**
     * Allows password_verify to be called as an instance method.
     *
     * @param  mixed $password
     * @return void
     */
    public function verifyPassword($password) {
        $result = false;

        if (password_verify($password, $this->pin)) {
            $result = true;
        }

        return $result;
    }
    
    /**
     * Updates the user's PIN.
     *
     * @param  String $new_pin: The new PIN.
     * @return void
     */
    public function updatePIN($new_pin) {
        // BCRYPT automatically salts the hashed password.
        $hashed_pin = password_hash($new_pin, PASSWORD_BCRYPT);
        Database::query("UPDATE users SET pin = :pin WHERE id = :id");
        Database::bind(':pin', $hashed_pin, PDO::PARAM_STR);
        Database::bind(':id', $this->id, PDO::PARAM_INT);
        try {
            Database::execute();
        } catch (PDOException $e) {
            throw new UserException($e->getMessage());
        }
        
    }

    public function updatePosition($position_id) {
        Database::query("UPDATE users SET user_position_id = :user_position_id WHERE id = :id");
        Database::bind(':user_position_id', $position_id, PDO::PARAM_INT);
        Database::bind(':id', $this->id, PDO::PARAM_INT);
        Database::execute();
    }

    public function updateExpectedClockIn($new_time) {
        Database::query("UPDATE users SET expected_clockin = :new_time WHERE id = :id");
        Database::bind(':new_time', $new_time, PDO::PARAM_STR);
        Database::bind(':id', $this->id, PDO::PARAM_INT);
        Database::execute();
    }

    public function updateExpectedClockOut($new_time) {
        Database::query("UPDATE users SET expected_clockout = :new_time WHERE id = :id");
        Database::bind(':new_time', $new_time, PDO::PARAM_STR);
        Database::bind(':id', $this->id, PDO::PARAM_INT);
        Database::execute();
    }

    public function updatePayrollID($payroll_id) {
        $payroll_id = $payroll_id == '' ? NULL : $payroll_id;
        Database::query("UPDATE users SET payroll_id = :payroll_id WHERE id = :id");
        Database::bind(':payroll_id', $payroll_id, PDO::PARAM_STR);
        Database::bind(':id', $this->id, PDO::PARAM_INT);
        Database::execute();
    }
    
    /**
     * Updates user's fields in db.
     *
     * @param  mixed $comments
     * @param  mixed $overtime
     * @param  mixed $first_name
     * @param  mixed $last_name
     * @param  mixed $username
     * @return void
     */
    public function updateFields($comments, $overtime, $first_name, $last_name, $username, $user_type, $user_position) {
        Database::query("UPDATE users SET comments = :comments, 
                                          overtime = :overtime,
                                          first_name = :first_name,
                                          last_name = :last_name,
                                          username = :username,
                                          user_type_id = :user_type_id,
                                          user_position_id = :user_position_id
                                          WHERE id = :id");
        Database::bind(':comments', $comments, PDO::PARAM_STR);
        Database::bind(':overtime', $overtime, PDO::PARAM_STR);
        Database::bind(':first_name', $first_name, PDO::PARAM_STR);
        Database::bind(':last_name', $last_name, PDO::PARAM_STR);
        Database::bind(':username', $username, PDO::PARAM_STR);
        Database::bind(':user_type_id', $user_type, PDO::PARAM_INT);
        Database::bind(':user_position_id', $user_position, PDO::PARAM_INT);
        Database::bind(':id', $this->id, PDO::PARAM_INT);
        Database::execute();
        
    }
    
    /**
     * Creates an instance of a user by their id.
     *
     * @param  int $id - The id of the user bound to the db.
     * @return void
     */
    public static function findByID($id) {
        $db = Database::getInstance()->db;
        $valid = true;
        $query = "SELECT id, in_building, first_name, last_name, username, pin, user_type_id, user_position_id, comments, expected_clockin, expected_clockout, expected_work_hours, payroll_id, overtime, user_status, created_at, organization FROM users WHERE id = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id);
        if ($statement->execute()) {
            if ($statement->rowCount() > 0) {
                if ($row = $statement->fetch()) {
                    $user = User::createByRow($row);
                    //$user = new User($row['id'], $row['username'], $row['user_type_id'], $row['pin']);
                } else { $valid = false; }
            } else { $valid = false; }
        } else { $valid = false; }

        return $valid ? $user : false;
    }

    public static function findByPosition($position_id) {
        Database::query("SELECT id, username, first_name, last_name, user_position_id, comments, expected_clockin, expected_clockout, payroll_id, overtime FROM users WHERE user_position_id = :id ORDER BY last_name");
        Database::bind(':id', $position_id, PDO::PARAM_INT);
        $stmt = Database::getStatement();
        $users = [];

        while ($row = $stmt->fetch()) {
            array_push($users, self::createByRow($row));
        }

        return $users;
    }
    
    /**
     * Creates an instance of user by their username.
     *
     * @param  mixed $username - User's username bound to db.
     * @return void
     */
    public static function findByUsername($username) {
        $db = Database::getInstance()->db;
        $valid = true;
        $query = "SELECT id, username, in_building, first_name, last_name, expected_clockin, expected_clockout, payroll_id, pin, user_type_id, user_position_id, comments, overtime, user_status FROM users WHERE username = :username";
        $statement = $db->prepare($query);
        $statement->bindValue(':username', $username);
        if ($statement->execute()) {
            if ($statement->rowCount() > 0) {
                if ($row = $statement->fetch()) {
                    $user = User::createByRow($row);
                    //$user = new User($row['id'], $row['username'], $row['user_type_id'], $row['pin']);
                } else { $valid = false; }
            } else { $valid = false; }
        } else { $valid = false; }

        return $valid ? $user : false;
    }

    public function isOnBreak() {
        $result = false;
        $a = Attendance::createWithDate($this, new DateTime());
        
        if ($a->getMorningOut() && !$a->getMorningIn()) {
            $result = true;
        } else if ($a->getLunchOut() && !$a->getLunchIn()) {
            $result = true;
        } else if ($a->getAfternoonOut() && !$a->getAfternoonIn()) {
            $result = true;
        } else if ($a->getNonPaidOut() && !$a->getNonPaidIn()) {
            $result = true;
        }

        return $result;
    }

    public function isIn() {
        $a = Attendance::createWithDate($this, new DateTime());
        
        // Not on break, and have a clock-in and not a clock-out.
        return !$this->isOnBreak() && $a->getClockIn() && !$a->getClockOut();
    }
    
    /**
     * Returns the last time a user clocked-in.
     * Returns false if result set is empty.
     *
     * @return void
     */
    public function getLastClockIn() {
        $result = false;
        $db = Database::getInstance()->db;
        $sql = "SELECT `username`, `attendances`.* FROM `users` 
                JOIN `attendances` 
                ON `users`.`id` = `user_id`
                WHERE `user_id` = {$this->id}
                AND DATE_FORMAT(`attendances`.`clockin`, '%Y%m%d') = DATE_FORMAT(NOW(), '%Y%m%d')
                LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        

        if ($row = $stmt->fetch()) {
            $updated_at = $row['updated_at'];

            switch ($updated_at) {
                case $row['clockin']:
                    $result = 'clockin';
                break;
                case $row['clockout']:
                    $result = 'clockout';
                break;
                case $row['morningin']:
                    $result = 'morningin';
                break;
                case $row['morningout']:
                    $result = 'morningout';
                break;
                case $row['lunchin']:
                    $result = 'lunchin';
                break;
                case $row['lunchout']:
                    $result = 'lunchout';
                break;
                case $row['afternoonin']:
                    $result = 'afternoonin';
                break;
                case $row['afternoonout']:
                    $result = 'afternoonout';
                break;
                case $row['non_paid_in']:
                    $result = 'non_paid_in';
                break;
                case $row['non_paid_out']:
                    $result = 'non_paid_out';
                break;
                default:
                    $result = false;
                break;
            }
        } 

        return $result;
    }

    public function getFormattedLastClockIn() {
        $db = Database::getInstance()->db;
        $sql = "SELECT `username`, `attendances`.* FROM `users` 
                JOIN `attendances` 
                ON `users`.`id` = `user_id`
                WHERE `user_id` = {$this->id}
                AND DATE_FORMAT(`attendances`.`clockin`, '%Y%m%d') = DATE_FORMAT(NOW(), '%Y%m%d')
                LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        
        if ($this->getUserType() != 'Guest') {
            $clock_in_string = '<h1 class="text-success">' . $this->getFirstName() . ': Clock-in</h1><i class="fa fa-clock-o text-warning" aria-hidden="true"></i><div class="text-warning" id="snack_time"></div>';
            $clock_out_string = '<h1 class="text-success">' . $this->getFirstName() . ': Out for the day</h1><i class="fa fa-clock-o text-warning" aria-hidden="true"></i><div class="text-warning" id="snack_time"></div>';
        } else {
            $clock_in_string = '<h1 class="text-success">' . $this->getFullName() . ': Sign-in</h1><i class="fa fa-clock-o text-warning" aria-hidden="true"></i><div class="text-warning" id="snack_time"></div>';
            $clock_out_string = '<h1 class="text-success">' . $this->getFullName() . ': Sign-out</h1><i class="fa fa-clock-o text-warning" aria-hidden="true"></i><div class="text-warning" id="snack_time"></div>';
        }

        if ($row = $stmt->fetch()) {
            $updated_at = $row['updated_at'];

            switch ($updated_at) {
                case $row['clockin']:
                    $result = $clock_in_string;
                break;
                case $row['clockout']:
                    $result = $clock_out_string;
                break;
                case $row['morningin']:
                    $result = '<h1 class="text-success">' . $this->getFirstName() . ': In from morning break</h1><i class="fa fa-home text-warning" aria-hidden="true"></i><div class="text-warning" id="snack_time"></div>';
                break;
                case $row['morningout']:
                    $result = '<h1 class="text-success">' . $this->getFirstName() . ': Out for morning break</h1><i class="fa fa-coffee text-warning" aria-hidden="true"></i><div class="text-warning" id="snack_time"></div>';
                break;
                case $row['lunchin']:
                    $result = '<h1 class="text-success">' . $this->getFirstName() . ': In from lunch</h1><i class="fa fa-home text-warning" aria-hidden="true"></i><div class="text-warning" id="snack_time"></div>';
                break;
                case $row['lunchout']:
                    $result = '<h1 class="text-success">' . $this->getFirstName() . ': Out for lunch</h1><i class="fa fa-cutlery text-warning" aria-hidden="true"></i><div class="text-warning" id="snack_time"></div>';
                break;
                case $row['afternoonin']:
                    $result = '<h1 class="text-success">' . $this->getFirstName() . ': In from afternoon break</h1><i class="fa fa-home text-warning" aria-hidden="true"></i><div class="text-warning" id="snack_time"></div>';
                break;
                case $row['afternoonout']:
                    $result = '<h1 class="text-success">' . $this->getFirstName() . ': Out for afternoon break</h1><i class="fa fa-coffee text-warning" aria-hidden="true"></i><div class="text-warning" id="snack_time"></div>';
                break;
                case $row['non_paid_out']:
                    $result = '<h1 class="text-success">' . $this->getFirstName() . ': Out for unpaid break</h1><i class="fa fa-clock-o text-warning" aria-hidden="true"></i><div class="text-warning" id="snack_time"></div>';
                break;
                case $row['non_paid_in']:
                    $result = '<h1 class="text-success">' . $this->getFirstName() . ': In from unpaid break</h1><i class="fa fa-clock-o text-warning" aria-hidden="true"></i><div class="text-warning" id="snack_time"></div>';
                break;
            }
        } 

        return $result;
    }
    
    /**
     * setUserPin
     *
     * @param  String $pin:         New pin.
     * @param  String $confirm_pin: New confirmation pin.
     * @param  String $old_pin:     Old pin that needs to be validated first.
     * @return void
     */
    public function setUserPin($pin, $confirm_pin, $old_pin) {

        if (password_verify($old_pin, $this->pin)) {
            if ($pin === $confirm_pin) {
                Database::query("UPDATE users SET pin = :pin");
                Database::bind(':pin', $pin, PDO::PARAM_STR);
                Database::execute();
            } else {
                throw new UserException('PINs do not match.');
            }
        } else {
            throw new UserException('Incorrect PIN.');
        }
    }

    public static function getAdmins() {
        $array = [];
        $db = Database::getInstance()->db;
        $query = "SELECT id, username, in_building, organization, first_name, last_name, pin, expected_clockin, expected_clockout, payroll_id, user_type_id, user_position_id, comments, user_status, overtime, created_at FROM users WHERE user_type_id = :user_type_id AND user_status = 'active'";
        $statement = $db->prepare($query);
        $statement->bindValue(':user_type_id', ADMIN);
        $statement->execute();

        while ($row = $statement->fetch()) {
            $user = User::createByRow($row);
            //$temp_array = array('id' => $row['id'], 'username' => $row['username'], 'password' => $row['password']);
            //$user = new User($row['id'], $row['username'], $row['user_type_id'], $row['pin']);
            array_push($array, $user);
        }

        return $array;
    }

    // Returns an array of all users in the database.
    public static function getAllUsers() {
        $db = Database::getInstance()->db;
        $array = [];
        $query = "SELECT users.id, username, in_building, organization, first_name, last_name, expected_clockin, expected_clockout, expected_work_hours, payroll_id, pin, user_type_id, user_position_id, users.comments, overtime, user_status, users.created_at
                  FROM (SELECT * FROM users WHERE user_status = 'active') as users LEFT JOIN attendances
                  ON users.id = `user_id`
                  AND DATE_FORMAT(NOW(), '%Y%m%d') = DATE_FORMAT(attendances.intended_date, '%Y%m%d')
                  ORDER BY FIELD(user_type_id, 3) DESC, attendances.clockout, first_name ASC";
        $statement = $db->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch()) {
            $user = self::createByRow($row);
            array_push($array, $user);
        }

        return $array;

    }

    public static function getAllEmployees($limit='', $order='first_name', $offset='') {
        $db = Database::getInstance()->db;
        $array = [];
        $query = "SELECT id, username, in_building, organization, first_name, last_name, pin, expected_clockin, expected_clockout, expected_work_hours, payroll_id, user_type_id, user_position_id, comments, user_status, overtime, created_at
                  FROM users
                  WHERE user_type_id <> 3
                  AND user_type_id <> 4
                  AND user_status = 'active'
                  ORDER BY $order $limit $offset";
        $statement = $db->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch()) {
            $user = self::createByRow($row);
            array_push($array, $user);
        }

        return $array;
    }

    public static function getAllStudents($limit='', $offset='', $status='active') {
        $users = [];
        Database::query("SELECT id, username, in_building, organization, first_name, last_name, pin, expected_clockin, expected_clockout, expected_work_hours, payroll_id, user_type_id, user_position_id, comments, user_status, overtime, created_at
                         FROM users WHERE user_type_id = 4 AND user_status = '$status' ORDER BY id $limit $offset");
        foreach (Database::result_set() as $key => $value) {
            array_push($users, self::createByRow($value));
        }

        return $users;
    }

    public static function getEmployeesAndStudents($limit='', $status='active', $offset='') {
        $users = [];
        Database::query("SELECT id, username, in_building, first_name, last_name, pin, user_type_id, user_position_id, expected_work_hours, payroll_id, comments, user_status, overtime, organization, created_at
                         FROM users 
                         WHERE user_type_id <> 3
                         AND user_status = '$status'
                         ORDER BY first_name $limit $offset");
        foreach (Database::result_set() as $key => $value) {
            array_push($users, self::createByRow($value));
        }

        return $users;
    }

    public static function getAllInterns() {
        $users = [];
        Database::query("SELECT id, username, in_building, first_name, last_name, pin, user_type_id, user_position_id, expected_work_hours, payroll_id, comments, user_status, overtime, organization, created_at
                         FROM users 
                         WHERE user_type_id = 1
                         AND user_status = 'active'
                         ORDER BY first_name");
        foreach (Database::result_set() as $key => $value) {
            array_push($users, self::createByRow($value));
        }

        return $users;
    }

    public static function getInactiveEmployees($limit='', $offset='') {
        Database::query("SELECT id, username, in_building, first_name, last_name, pin, user_type_id, expected_clockin, expected_clockout, expected_work_hours, payroll_id, comments, user_status, overtime, created_at
                         FROM users
                         WHERE user_type_id <> 3
                         AND user_status = 'inactive'
                         ORDER BY user_type_id DESC, last_name ASC $limit $offset");

        $users = [];

        foreach (Database::result_set() as $key => $value) {
            array_push($users, self::createByRow($value));
        }

        return $users;
    }

    public static function getAllGuests($limit='', $offset='', $search='') {
        if (!empty($search)) {
            $start_date = substr($search, 0, strpos($search, ' '));
            $end_date = substr($search, strpos($search, ' ', strpos($search, ' ') + 1));
            Database::query("SELECT id, username, in_building, first_name, last_name, pin, user_type_id, user_position_id, comments, user_status, overtime, organization, created_at
                             FROM users WHERE user_type_id = 3 
                             AND DATE_FORMAT(`created_at`, '%Y%m%d') BETWEEN DATE_FORMAT(:start_date, '%Y%m%d') AND DATE_FORMAT(:end_date, '%Y%m%d') 
                             ORDER BY `created_at` DESC $limit $offset");
            Database::bind(':start_date', $start_date, PDO::PARAM_STR);
            Database::bind(':end_date', $end_date, PDO::PARAM_STR);
        } else {
            Database::query("SELECT id, username, in_building, first_name, last_name, pin, user_type_id, user_position_id, comments, user_status, overtime, organization, created_at
                             FROM users WHERE user_type_id = 3 ORDER BY `created_at` DESC $limit $offset");
        }
        $users = [];

        foreach (Database::result_set() as $key => $value) {
            array_push($users, self::createByRow($value));
        }

        return $users;
    }

    public function isAdmin() {
        return ($this->user_type_id == ADMIN);
    }
    
    /**
     * If there is are guest users in the database, this will return the last one.
     * Returns false if there are no guest users.
     *
     * @return void
     */
    public static function getLastGuestUser() {
        $result = false;
        $db = Database::getInstance()->db;
        $sql = "SELECT `users`.`id`, `first_name`, `last_name`, `username`, `in_building`, `type`, `organization` FROM `users`
                JOIN `user_types`
                ON `users`.`user_type_id` = `user_types`.`id`
                AND `user_types`.`id` = 3
                ORDER BY `id` DESC
                LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            $result = self::createByRow($row);
        }

        return $result;
    }
    
    /**
     * Inserts a new user into the database.
     *
     * @return User: The newly created user.
     */
    public static function addUser($username, $first_name, $last_name, $user_type_id, $user_position_id, $pin, $confirm_pin) {
        $hashed_pin = password_hash($pin, PASSWORD_BCRYPT);
        Database::query("INSERT INTO users(username, first_name, last_name, user_type_id, user_position_id, pin)
                         VALUES (:username, :first_name, :last_name, :user_type_id, :user_position_id, :pin)");
        Database::bind(':username', $username, PDO::PARAM_STR);
        Database::bind(':first_name', $first_name, PDO::PARAM_STR);
        Database::bind(':last_name', $last_name, PDO::PARAM_STR);
        Database::bind(':user_position_id', $user_position_id, PDO::PARAM_INT);
        Database::bind(':user_type_id', $user_type_id, PDO::PARAM_INT);
        Database::bind(':pin', $hashed_pin, PDO::PARAM_STR);

        if ($pin === $confirm_pin) {
            Database::execute();
        } else {
            throw new UserException('PINs do not match');
        }
    }

    public static function addGuestUser($username, $first_name, $last_name, $pin, $organization) {
        define('GUEST', 3);
        
        $hashed_pin = password_hash($pin, PASSWORD_BCRYPT);
        $db = Database::getInstance()->db;
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "INSERT INTO users (username, first_name, last_name, user_type_id, user_position_id, pin, organization) VALUES (:username, :first_name, :last_name, :user_type_id, :user_position_id, :pin, :organization)";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":username", $username, PDO::PARAM_STR);
        $stmt->bindValue(":first_name", $first_name, PDO::PARAM_STR);
        $stmt->bindValue(":last_name", $last_name, PDO::PARAM_STR);
        $stmt->bindValue(":pin", $hashed_pin, PDO::PARAM_STR);
        $stmt->bindValue(":user_type_id", GUEST, PDO::PARAM_INT);
        $stmt->bindValue(":user_position_id", UserPosition::last()->getID(), PDO::PARAM_INT);
        $stmt->bindValue(":organization", $organization, PDO::PARAM_STR);

        try {
            $stmt->execute();
        } catch(PDOException $e) {
            print 'Error: ' . $e->getMessage();
        }
    }

    public function setTotalBiWeeklyMinutes($date) {
        $date = "'" . $date . "'";
        Database::query("SELECT clockin, clockout, TIMESTAMPDIFF(MINUTE, clockin, clockout) AS time_in_minutes 
                         FROM attendances 
                         WHERE DATE_FORMAT(clockin, '%Y%m%d') BETWEEN DATE_FORMAT(DATE_SUB($date, INTERVAL 2 WEEK), '%Y%m%d')
                         AND DATE_FORMAT($date, '%Y%m%d') 
                         AND user_id = :user_id");
        Database::bind(':user_id', $this->id, PDO::PARAM_INT);

        $minutes = 0;

        foreach (Database::result_set() as $key => $value) {

            // If the int hour value is greater than or equal to 5 hours, take away 30 minutes to compensate for lunch break.
            $minutes += (((int)($value['time_in_minutes'] / 60)) >= 5) ? ($value['time_in_minutes'] - 30) : $value['time_in_minutes'];
        }

        $this->total_biweekly_minutes = $minutes;
    }

    public function getTotalBiWeeklyHours() {
        $minutes = $this->getTotalBiWeeklyMinutes();
        return (int)($minutes / 60);
    }

    public function getRemainingMinutes() {
        return ($this->getTotalBiWeeklyMinutes() % 60);
    }

    public function getHoursWithFraction() {
        $result = getApproxMinuteFraction($this->total_biweekly_minutes % 60);

        if ($result == 50)  {
            $result /= 10;
        }

        return ($result == 0) ? 0 : $this->getTotalBiWeeklyHours() . '.' . $result;
    }

    public function getActualHours() {
        $minutes = $this->getRemainingMinutes();

        if ($minutes < 10) {
            $minutes = '0' . $minutes;
        }
        return $this->getTotalBiWeeklyHours() . 'h ' . $minutes . 'm';
    }
    
    /**
     * Total bi-weekly hours of all employees.
     *
     * @param  mixed $date
     * @return void
     */
    public static function getTotalHours($date) {
        $hours = 0;
        $minutes = 0;
        $minute_fraction = 0;

        foreach (self::getAllEmployees() as $user) {
            $user->setTotalBiWeeklyMinutes($date);
            $minutes += $user->getTotalBiWeeklyMinutes();
            $minute_fraction += getApproxMinuteFraction(($user->getTotalBiWeeklyMinutes() % 60));
        }

        $hours = (int)($minutes / 60);
        //$minutes %= 60;

        return ($minute_fraction % 100 == 50) ? $hours . '.' . ($minute_fraction % 100) / 10 : $hours . '.' . $minute_fraction % 100;
    }
    
    public static function getTotalActualHours($date) {
        $minutes = 0;

        foreach (self::getAllEmployees() as $user) {
            $user->setTotalBiWeeklyMinutes($date);
            $minutes += $user->getTotalBiWeeklyMinutes();
        }

        $hours = (int)($minutes / 60);
        $minutes %= 60;

        return $hours . 'h ' . $minutes . 'm';
    }
    
    /**
     * getTotalDayMinutes
     *
     * @param  DateTime $date
     * @return void
     */
    public function getTotalDayMinutes($date) {
        $formatted_date = $date->format('Y-m-d');
        Database::query("SELECT clockin, clockout, TIMESTAMPDIFF(MINUTE, clockin, clockout) AS time_in_minutes 
                         FROM attendances 
                         WHERE DATE_FORMAT(clockin, '%Y%m%d') = DATE_FORMAT(:formated_date, '%Y%m%d') 
                         AND user_id = :id");
        Database::bind(':id', $this->id, PDO::PARAM_INT);
        Database::bind(':formatted_date', $formatted_date, PDO::PARAM_STR);

        $row = Database::getRow();
        return $row['time_in_minutes'];
    }

    public function search($string, $limit='', $offset='', $status='active') {
        Database::query("SELECT `id`, `first_name`, `last_name`, `username` 
                         FROM `users` 
                         WHERE `first_name` LIKE :search AND `user_type_id` <> 3 AND `user_status` = '$status'
                         OR `last_name` LIKE :search AND `user_type_id` <> 3 AND `user_status` = '$status'
                         OR `username` LIKE :search AND `user_type_id` <> 3 AND `user_status` = '$status'
                         ORDER BY `first_name` $limit $offset");
        Database::bind(':search', '%' . $string . '%', PDO::PARAM_STR);
        $stmt = Database::getStatement();
        $array = [];

        while ($row = $stmt->fetch()) {
            array_push($array, self::createByRow($row));
        }

        return $array;
    }

    public function updateExpectedWorkHours($work_hours) {
        Database::query("UPDATE users SET expected_work_hours = :expected_work_hours WHERE id = :id");
        Database::bind(':expected_work_hours', $work_hours, PDO::PARAM_INT);
        Database::bind(':id', $this->id, PDO::PARAM_INT);
        Database::execute();
    }

    /**
     * Checks whether a username exists in the database.
     *
     * @param  String $username: The user name of the selected user.
     * @return bool
     */
    public static function usernameExists($username) {
        Database::query("SELECT username FROM users WHERE username = :username");
        Database::bind(':username', $username, PDO::PARAM_STR);
        $row = Database::getRow();
        return ($username === $row['username']);
    }
    
    public function offboard() {
        Database::query("UPDATE users SET user_status = 'inactive' WHERE id = :id");
        Database::bind(':id', $this->id, PDO::PARAM_INT);
        Database::execute();
    }

    public function onboard() {
        Database::query("UPDATE users SET user_status = 'active' WHERE id = :id");
        Database::bind(':id', $this->id, PDO::PARAM_INT);
        Database::execute();
    }

    public function deleteGuest() {
        Database::query("DELETE FROM users WHERE user_type_id = 3 AND id = :id");
        Database::bind(':id', $this->id, PDO::PARAM_INT);
        Database::execute();
    }

    public static function deleteAllGuests() {
        Database::query("DELETE FROM users WHERE user_type_id = 3");
        Database::execute();
    }

    public function __toString() {
        return $this->username;
    }

    private function fill($row) {
        $this->id = $row['id'];
        $this->username = $row['username'];
        $this->first_name = $row['first_name'];
        $this->last_name = $row['last_name'];
        $this->user_type_id = $row['user_type_id'];
        $this->user_position_id = $row['user_position_id'];
        $this->pin = $row['pin'];
        $this->building_status = $row['in_building'];
        $this->comments = $row['comments'];
        $this->organization = $row['organization'];
        $this->overtime = $row['overtime'];
        $this->expected_clockin = $row['expected_clockin'];
        $this->expected_clockout = $row['expected_clockout'];
        $this->expected_work_hours = $row['expected_work_hours'];
        $this->payroll_id = $row['payroll_id'];
        $this->status = $row['user_status'];
        $this->created_at = $row['created_at'];
    }
}

?>