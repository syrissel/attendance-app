<?php
/**
 * General utility functions.
 * 
 * @author: Steph Mireault
 * @date:   July 22, 2020
 */

// Checks if iPad iOS version is 9 or lower.
function isOS931() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $ipad_string = strstr($user_agent, 'iPad');
    $ios_string = strstr($ipad_string, 'OS');
    $version = substr($ios_string, strpos($ios_string, ' '), 6);
    return ((strlen($ipad_string) > 0) && (strlen($ios_string) > 0) && (strlen($version) > 0) && ((int)$version <= 9));
}

// Gets the approximate percentage of the fractional hour worked.
function getApproxMinuteFraction($minutes) {

    $fraction = 0;

    if ($minutes >= 9 && $minutes <= 23) {
        $fraction = 25;
    } else if ($minutes >= 24 && $minutes <= 38) {
        $fraction = 5;
    } else if ($minutes >= 39 && $minutes <= 59) {
        $fraction = 75;
    }

    return $fraction;
}

// Gets the exact fraction amount of the partial hour worked.
function getPreciseMinuteFraction($minutes) {

    $fraction = round(($minutes / 60), 2) * 100;

    // If the number has a trailing zero, remove it.
    if (substr(strval($fraction), 1, 1) === '0') {
        $fraction /= 10;
    }
    // Add decimal point before returning value.
    return '.' . $fraction;
}


function getSemanticHours($minutes) {
    $hours = (int)($minutes / 60);
    $remaining_minutes = $minutes % 60;

    // if ($remaining_minutes < 10) {
    //     $remaining_minutes = '0' . $remaining_minutes;
    // }

    return $hours < 0 ? "0h 0m" : $hours . 'h ' . $remaining_minutes . 'm';
}

function getTotalPaidHours($minutes) {
    $hours = (int)($minutes / 60);
    $remaining_minutes = $minutes % 60;

    return (getApproxMinuteFraction($remaining_minutes) == 0) ? $hours : $hours . '.' . getApproxMinuteFraction($remaining_minutes);
}

function getTimeDiffInMinutes($start, $end, $break=true) {
    $minutes = (strtotime($end) - strtotime($start)) / 60;

    if ($break) {
        if ((int)($minutes / 60) >= 5) {
            $minutes -= 30;
        }
    }

    return $minutes;
}

function roundTimeStamp($time_string) {
    $date = new DateTime($time_string);
    $minutes = $date->format('i');
    $result = 0;

    if ($minutes <= 8) {
        $result = 0;
    } else if ($minutes >= 9 && $minutes <= 23) {
        $result = 15;
    } else if ($minutes >= 24 && $minutes <= 38) {
        $result = 30;
    } else if ($minutes >= 39 && $minutes <= 53) {
        $result = 45;
    } else {
        $result = 60;
    }

    $date->sub(new DateInterval("PT{$minutes}M"));
    $date->add(new DateInterval("PT{$result}M"));
    return $date->format('H:i');
}

/**
 * stringToDate
 *
 * @param  mixed $string
 * @return String
 */
function formatPunchInTime($string) {
    $result = "<span class='font-weight-light py-1'>-</span>";

    if (!empty($string)) {
        $result = "<span class='font-weight-bold py-1'>" . strval(date('H:i', strtotime($string))) . "</span>";
    }

    return $result;
}

// https://github.com/ttodua/useful-php-scripts 
function IMPORT_TABLES($host,$user,$pass,$dbname, $sql_file_OR_content){
    $error = "";
    $valid = true;
    set_time_limit(3000);
    $SQL_CONTENT = (strlen($sql_file_OR_content) > 300 ?  $sql_file_OR_content : file_get_contents($sql_file_OR_content)  );  
    $allLines = explode("\n",$SQL_CONTENT); 
    $mysqli = new mysqli($host, $user, $pass, $dbname); if (mysqli_connect_errno()){echo "Failed to connect to MySQL: " . mysqli_connect_error();} 
        $zzzzzz = $mysqli->query('SET foreign_key_checks = 0');	        preg_match_all("/\nCREATE TABLE(.*?)\`(.*?)\`/si", "\n". $SQL_CONTENT, $target_tables); foreach ($target_tables[2] as $table){$mysqli->query('DROP TABLE IF EXISTS '.$table);}         $zzzzzz = $mysqli->query('SET foreign_key_checks = 1');    $mysqli->query("SET NAMES 'utf8'");	
    $templine = '';	// Temporary variable, used to store current query
    foreach ($allLines as $line)	{											// Loop through each line
        if (substr($line, 0, 2) != '--' && $line != '') {$templine .= $line; 	// (if it is not a comment..) Add this line to the current segment
            if (substr(trim($line), -1, 1) == ';') {		// If it has a semicolon at the end, it's the end of the query
                if(!$mysqli->query($templine)){ 
                    $error .= 'Error performing query \'<strong>' . $templine . '\': ' . $mysqli->error . '<br /><br />';
                    $valid = false;
                }  
                
                $templine = ''; // set variable to empty, to start picking up the lines after ";"
            }
        }
    }

    if ($error) {
        return $error;
    }

    return false;
}   //see also export.php 

// Return float value of minutes converted into hours.
function minutesToHours($minutes) {
    $hours = ($minutes / 60);
    return $hours;
}
