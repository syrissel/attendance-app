<?php
use PHPUnit\Framework\TestCase;
require('../classes/classes.php');

class AttendanceTest extends TestCase {
    // public function testTotalMinutes() {
    //     $a = Attendance::findByID(195);
    //     $this->assertSame(482, $a->getTotalMinutes());
    // }

    // public function testTimeDiff() {
    //     $a = Attendance::findByID(234);
    //     $expected = 465;
    //     $actual = getTimeDiffInMinutes($a->roundTime('clockin'), $a->roundTime('clockout'));
    //     $this->assertSame($expected, $actual);
    // }

    // public function testRoundTime() {
    //     $a = Attendance::findByID(234);
    //     $expected_clockin = '2020-07-14 08:15:00';
    //     $expected_clockout = '2020-07-14 16:30:00';
    //     $actual_clockin = $a->roundTime('clockin');
    //     $actual_clockout = $a->roundTime('clockout');

    //     $this->assertSame($expected_clockin, $actual_clockin);
    //     $this->assertSame($expected_clockout, $actual_clockout);
    // }

    // public function testGetRoundedClockIn() {
    //     $attendance = Attendance::findByID(35);
    //     $expected = '08:00';

    //     $this->assertSame($expected, $attendance->getRoundedClockIn());
    // }

    // public function testGetRoundedClockOut() {
    //     $attendance = Attendance::findByID(35);
    //     $expected = '16:00';

    //     $this->assertSame($expected, $attendance->getRoundedClockOut());
    // }

    // public function testException() {
    //     $this->expectException(AttendanceException::class);

    //     $a = Attendance::findByID(103);
    //     $a->getRoundedClockOut();
    //     throw new AttendanceException();
    // }

    // public function testCheckClockOut() {
    //     $a = Attendance::findByID(88);

    //     $this->assertSame(true, $a->checkClockOut());
    // }

    public function testVerifyFields() {
        $a = Attendance::findByID(194);
        $expected = true;
        $post_array = array(
            "clockin"=> "",
            "clockout"=> "",
            "morningin"=> "2020-08-12 21:29:16",
            "morningout"=> "2020-08-12 21:29:07",
            "lunchin"=> "",
            "lunchout"=> "",
            "afternoonin"=> "",
            "afternoonout"=> "",
            "id"=> "234",
            "created_at"=> "2020-08-12 21:28:56",
            "absent"=> "true",
            "reason"=> "1",
            "absent_length_from"=> "",
            "absent_length_to"=> ""
        );

        echo 'clockin<br />' . $post_array['clockin'];
        echo 'clockout<br />' . $post_array['clockout'];
        echo 'morningin<br />' . $post_array['morningin'];
        echo 'morningout<br />' . $post_array['morningout'];
        echo 'lunchin<br />' . $post_array['lunchin'];
        echo 'lunchout<br />' . $post_array['lunchout'];
        echo 'afternoonin<br />' . $post_array['afternoonin'];
        echo 'afternoonout<br />' . $post_array['afternoonout'];
        echo 'created_at<br />' . $post_array['created_at'];
        $actual = $a->verifyFields($a, $post_array);

        $this->assertSame($expected, $actual);
    }
}