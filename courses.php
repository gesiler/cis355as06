<?php

/*
  filename 	: courses.php
  author   	: george corser
  course   	: cis355 (winter2016)
  description	: print fomatted output from JSON object 
                  returned by SVSU Courses API
  input    	: api.svsu.edu/courses
 */

// suppress notices
ini_set('error_reporting', 
    E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED); 

main();

#-----------------------------------------------------------------------------
# FUNCTIONS
#-----------------------------------------------------------------------------

function main() {

    // echo html head section
    echo '<html>';

    // open html body section
    echo '<body>';

    // in html body section, if gpcorser's schedule, then print gpcorser's heading, else print general CS/CIS/CSIS heading
    if (!strcmp($_GET['instructor'], 'gpcorser')) {
        echo '<h1 align="center">George Corser, PhD</h1>';
        echo '<h2 align="center">CURRENT COURSES</h2>';
    } else {
        echo '<h1 align="center">SVSU/CSIS Department</h1>';
        echo '<h2 align="center">';
        echo $_GET['prefix'] ? ' - Prefix: ' . strtoupper($_GET['prefix']) : "";
        echo $_GET['courseNumber'] ? ' - Course Number: ' . $_GET['courseNumber'] : "";
        echo $_GET['instructor'] ? ' - Instructor: ' . strtoupper($_GET['instructor']) : "";
        echo '</h2>';
    }

    // if user entered something in a search box, then call printCourses() to filter 
    if ($_GET['prefix'] != "" || $_GET['courseNumber'] != "" || $_GET['instructor'] != "" || $_GET['day' != ""]) {
        printCourses($_GET['prefix'], $_GET['courseNumber'], $_GET['instructor'], $_GET['day']);
    }
    // otherwise call printSemester() for all courses for each semester
    else {
        echo "<h3>Spring</h3>";
        printSemester("19/SP");
        echo "<h3>Summer</h3>";
        printSemester("19/SU");
        echo "<h3>Fall</h3>";
        printSemester("19/FA");
        echo "<h3>Winter</h3>";
        printSemester("20/WI");
    }

    // display the entry form for next search
    //printForm(); 
    // display button for course search
    echo '<a href="coursesearch.php" class="btn btn-primary">Search</a>';

    // close html body section
    echo '</body>';
    echo '</html>';
}

#-----------------------------------------------------------------------------
// print all courses for a given filter

function printCourses($prefix, $courseNumber, $instructor, $day) {

    // call printListing() for each semester using all parameters

    $term = "19/SP";
    $string = "https://api.svsu.edu/courses?prefix=$prefix&courseNumber=$courseNumber&term=$term&instructor=$instructor";
    echo "<h3>2019 - Spring</h3>";
    printListing($string, $day);

    $term = "19/SU";
    $string = "https://api.svsu.edu/courses?prefix=$prefix&courseNumber=$courseNumber&term=$term&instructor=$instructor";
    echo "<h3>2019 - Summer</h3>";
    printListing($string, $day);

    $term = "19/FA";
    $string = "https://api.svsu.edu/courses?prefix=$prefix&courseNumber=$courseNumber&term=$term&instructor=$instructor";
    echo "<h3>2019 - Fall</h3>";
    printListing($string, $day);

    $term = "20/WI";
    $string = "https://api.svsu.edu/courses?prefix=$prefix&courseNumber=$courseNumber&term=$term&instructor=$instructor";
    echo "<h3>2019 - Winter</h3>";
    printListing($string, $day);
}

#-----------------------------------------------------------------------------
// print all CS/CIS/CSIS courses for a given semester

function printSemester($term) {

    // note: printSemester() is only called when user has not entered anything in entry form
    // print all CIS courses for semester
    $string = "https://api.svsu.edu/courses?prefix=CIS&term=$term";
    printListing($string, $day);

    // print all CS courses for semester
    $string = "https://api.svsu.edu/courses?prefix=CS&term=$term";
    printListing($string, $day);

    // print all CSIS courses for semester
    $string = "https://api.svsu.edu/courses?prefix=CSIS&term=$term";
    printListing($string, $day);

    // print all CSIS-related MATH courses for semester
    // $string ="https://api.svsu.edu/courses?prefix=MATH&courseNumber=103&term=$term";
    // printListing($string);
    // $string ="https://api.svsu.edu/courses?prefix=MATH&courseNumber=120A&term=$term";
    // printListing($string);
    // $string ="https://api.svsu.edu/courses?prefix=MATH&courseNumber=120B&term=$term";
    // printListing($string);
    // $string ="https://api.svsu.edu/courses?prefix=MATH&courseNumber=140&term=$term";
    // printListing($string);
    // $string ="https://api.svsu.edu/courses?prefix=MATH&courseNumber=161&term=$term";
    // printListing($string);
    // $string ="https://api.svsu.edu/courses?prefix=MATH&courseNumber=223&term=$term";
    // printListing($string);
    // $string ="https://api.svsu.edu/courses?prefix=MATH&courseNumber=300&term=$term";
    // printListing($string);
}

#-----------------------------------------------------------------------------
// print an html table for one single query of the api

function printListing($apiCall, $day) {

    $json = curl_get_contents($apiCall);
    // $json = curl_get_contents("https://api.svsu.edu/courses?prefix=CIS&term=16/SP");
    // the line of code below suddenly stopped working!
    // $json = file_get_contents($apiCall); 
    $obj = json_decode($json);

    if (!($obj->courses == null)) {

        echo "<table border='3' width='100%'>";

        if (!$day) {
            $days = array('M', 'MW', 'T', 'TR', 'W', 'R', 'F', '', ' ');
        } else {
            if (strcasecmp($day, 'M') == 0) {
                $days = array('M', 'MW');
            } else if (strcasecmp($day, 'T') == 0) {
                $days = array('T', 'TR');
            } else if (strcasecmp($day, 'W') == 0) {
                $days = array('MW', 'W');
            } else if (strcasecmp($day, 'R') == 0) {
                $days = array('TR', 'R');
            } else {
                $days = array($day);
            }
        }

        foreach ($days as $day){

        
            foreach ($obj->courses as $course) {

                $continue = false;
                if ($course->meetingTimes[0]->days) {
                    if (strcasecmp($course->meetingTimes[0]->days, $day) == 0) {
                        $continue = true;
                    }
                } else {
                    if (strcasecmp($course->meetingTimes[1]->days, $day) == 0) {
                        $continue = true;
                    }
                }
                if (!$continue) {
                    continue;
                }
/*
                $building = strtoupper(trim($_GET['building']));
                $buildingMatch = false;
                $thisBuilding0 = trim($course->meetingTimes[0]->building);
                $thisBuilding1 = trim($course->meetingTimes[1]->building);
                if ($building && ($thisBuilding0 == $building || $thisBuilding1 == $building))
                    $buildingMatch = true;
                if (!($building))
                    $buildingMatch = true;
                if (!$buildingMatch)
                    continue;

                $room = strtoupper(trim($_GET['room']));
                $roomMatch = false;
                $thisroom0 = trim($course->meetingTimes[0]->room);
                $thisroom1 = trim($course->meetingTimes[1]->room);
                if ($room && ($thisroom0 == $room || $thisroom1 == $room))
                    $roomMatch = true;
                if (!($room))
                    $roomMatch = true;
                if (!$roomMatch)
                    continue;
*/
                // different <tr bgcolor=...> for each professor
                switch ($course->meetingTimes[0]->instructor) {
                    case "james":            // 1
                        $printline = "<tr bgcolor='#B19CD9'>";  // pastel purple
                        break;
                    case "icho":             // 2
                        $printline = "<tr bgcolor='lightblue'>";  // light blue
                        break;
                    case "krahman":           // 3 
                        $printline = "<tr bgcolor='pink'>";  // pink
                        break;
                    case "gpcorser":           // 4
                        $printline = "<tr bgcolor='yellow'>";   // yellow
                        break;
                    case "pdharam":           // 5
                        $printline = "<tr bgcolor='#77DD77'>";  // pastel green (light green)
                        break;
                    case "amulahuw":           // 6
                        $printline = "<tr bgcolor='#FFB347'>";  // pastel orange
                        break;
                    default:
                        $printline = "<tr>"; // no background color
                }

                $printline .= "<td width='10%'>" . $course->prefix . " " . $course->courseNumber . "*" . $course->section . "</td>";
                $printline .= "<td width='40%'>" . $course->title . " (" . $course->lineNumber . ")" . "</td>";
                $printline .= "<td width='5%'>" . $course->credit . "</td>";
                $printline .= "<td width='10%'>Av:" . $course->seatsAvailable . " (" . $course->capacity . ")" . "</td>";

                // print day and time column
                if ($course->meetingTimes[0]->days) {
                    $printline .= "<td width='15%'>" . $course->meetingTimes[0]->days . " " . $course->meetingTimes[0]->startTime;
                    // $printline .= . "<br /> " . $course->meetingTimes[1]->days . " " . $course->meetingTimes[1]->startTime ;
                    $printline .= "</td>";
                } else {
                    $printline .= "<td width='15%'>";
                    $printline .= $course->meetingTimes[1]->days . " " . $course->meetingTimes[1]->startTime . "</td> ";
                }

                // print building and room column
                $printline .= "<td width='10%'>";
                if (substr($course->section, -2, 1) == "9")
                    $printline .= "(Online)";
                else
                if (substr($course->section, -2, 1) == "7")
                    $printline .= $course->meetingTimes[1]->building . " " . $course->meetingTimes[1]->room;
                else
                    $printline .= $course->meetingTimes[0]->building . " " . $course->meetingTimes[0]->room;
                $printline .= "</td>";

                // print instructor column
                $printline .= "<td width='10%'>" . $course->meetingTimes[0]->instructor . "</td>";
                $printline .= "</tr>";
                echo $printline;
            } // end inner foreach
        } // end outer foreach
        echo "</table>";
        echo "<br/>";
    } // end if (!($obj->courses == null))
    else {
        echo "No courses fit search criteria";
        echo "<br />";
    }
}

#-----------------------------------------------------------------------------
// read file into a string

function curl_get_contents($url) {

    // alternative to file_get_contents

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);

    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}
