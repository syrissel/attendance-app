<?php
require_once('classes.php');

class ClockInType {
    public static function getClockInType($clock_in_type) {
        switch ($clock_in_type) {
            case 'clockin':
                $clock_in_type = 'Clock-in';
                break;
            case 'clockout':
                $clock_in_type = 'Clock-out';
                break;
            case 'morningin':
                $clock_in_type = 'Back from Break';
                break;
            case 'morningout':
                $clock_in_type = 'Morning Break';
                break;
            case 'lunchin':
                $clock_in_type = 'Back from Lunch';
                break;
            case 'lunchout':
                $clock_in_type = 'Lunch Break';
                break;
            case 'afternoonin':
                $clock_in_type = 'Back from Break';
                break;
            case 'afternoonout':
                $clock_in_type = 'Afternoon Break';
                break;
            case 'non_paid_out':
                $clock_in_type = 'Unpaid Break';
                break;
            case 'non_paid_in':
                $clock_in_type = 'Back from Unpaid Break';
                break;
        }

        return $clock_in_type;
    }
}
?>