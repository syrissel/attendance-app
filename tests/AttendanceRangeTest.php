<?php
use PHPUnit\Framework\TestCase;
require('../classes/classes.php');

class AttendanceRangeTest extends TestCase {
    public function testAddTotalMinutes() {
        $start_date = '2020-07-26';
        $end_date = '2020-08-08';
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
    
        // Does not include end date.
        $end = $end->modify('+1 day');
        $period = new DatePeriod($start, new DateInterval('P1D'), $end);
        $user = User::findByID(22);
        $ar = AttendanceRange::create($period, $user);
        
        // foreach ($employees as $user) {
        //     //$user->setTotalBiWeeklyMinutes($end_date);
        //     array_push($ar, AttendanceRange::create($period, $user));
        // }

        $expected = 2250;
        $actual = $ar->getTotalMinutes();

        $this->assertSame($expected, $actual);
    }


}