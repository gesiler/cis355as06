<?php

printForm(); 

#-----------------------------------------------------------------------------
// display the entry form for course search
function printForm(){
    echo '<a href="https://www.github.com/gesiler/cis355as06/">GitHub</a>';
    echo '</br>';
	echo '<h2>Course Lookup</h2>';
	
	// print user entry form
	echo "<form action='courses.php'>";
	echo "Course Prefix (Department)<br/>";
	echo "<input type='text' placeholder='CS' name='prefix'><br/>";
	echo "Course Number<br/>";
	echo "<input type='text' placeholder='116' name='courseNumber'><br/>";
	echo "Instructor<br/>";
    	echo "<input type='text' placeholder='gpcorser' name='instructor'><br/>";
    	echo "Day of Week<br/>";
    	echo "<input type='radio' name='day' value='M'>Monday<br/>";
    	echo "<input type='radio' name='day' value='T'>Tuesday<br/>";
    	echo "<input type='radio' name='day' value='W'>Wednesday<br/>";
    	echo "<input type='radio' name='day' value='R'>Thursday<br/>";
    	echo "<input type='radio' name='day' value='F'>Friday<br/>";
    	echo "<input type='radio' name='day' value='' checked>All<br/>";
	//echo "Building/Room<br/>";
	//echo "<input type='text' name='building'>";
	//echo "<input type='text' name='room'><br/>";
	echo "<input type='submit' value='Submit'>";
	echo "</form>";
}
