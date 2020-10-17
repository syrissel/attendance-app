<?php
require_once('classes/classes.php');
$attendances = Attendance::getSickDays();
$responses = [];

foreach ($attendances as $a) {
    $intended_date = new DateTime($a->getIntendedDate());
    $description = '';
    if (!$a->isFullDay()) {
        if ($a->getArriveEarly()) {
            $date = new DateTime($a->getArriveEarly());
            $description .= 'Arriving early at ' . $date->format('H:i') . "\n";
        }
        if ($a->getArriveLate()) {
            $date = new DateTime($a->getArriveLate());
            $description .= 'Arriving late at ' . $date->format('H:i') . "\n";
        }
        if ($a->getLeaveEarly()) {
            $date = new DateTime($a->getLeaveEarly());
            $description .= 'Leaving early at ' . $date->format('H:i') . "\n";
        }
    } else {
        $description = 'All day';
    }
    array_push($responses, ['title' => "{$a->getUser()->getFirstName()} - {$a->getAbsentReason()}", 
                            'start' => $intended_date->format('Y-m-d'), 
                            'end' => $intended_date->format('Y-m-d'), 
                            'attendance_id' => $a->getID(),
                            'description' => $description]);
}

echo json_encode($responses);
