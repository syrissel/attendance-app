<?php
use PHPUnit\Framework\TestCase;
require('../classes/classes.php');

class UserTest extends TestCase {

    // public function testPushAndPop()
    // {
    //     $stack = [];
    //     $this->assertSame(0, count($stack));

    //     array_push($stack, 'foo');
    //     $this->assertSame('foo', $stack[count($stack)-1]);
    //     $this->assertSame(1, count($stack));

    //     $this->assertSame('foo', array_pop($stack));
    //     $this->assertSame(0, count($stack));
    // }

    public function testUserHours() {
        Database::query("SELECT clockin, clockout, TIMESTAMPDIFF(MINUTE, clockin, clockout) AS time_in_minutes 
                         FROM attendances 
                         WHERE DATE_FORMAT(clockin, '%Y%m%d') BETWEEN DATE_FORMAT(DATE_SUB('2020-07-25', INTERVAL 2 WEEK), '%Y%m%d')
                         AND DATE_FORMAT('2020-07-25', '%Y%m%d') 
                         AND user_id = 180");
        Database::execute();

        $total_minutes = 0;

        foreach (Database::result_set() as $key => $value) {
            $total_minutes += $value['time_in_minutes'];
        }

        $user = User::findByID(180);
        $user->setTotalBiWeeklyHours('2020-07-25');

        $remaining_minutes = $user->getTotalBiWeeklyMinutes();

        $this->assertSame(81, $user->getTotalBiWeeklyHours());
        $this->assertSame(4873, $total_minutes);
        $this->assertSame(13, $remaining_minutes);
    }
}