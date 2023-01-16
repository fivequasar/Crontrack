<?php
$inactive = 5; //This is the timer, if the user does not do anything for 8 minutes(500 seconds) it will kick them out and they will hace to login again.
ini_set('session.gc_maxlifetime', $inactive);
session_start();

if (isset($_SESSION["admin_number"])){
//Since we have already assigned a fixed pattern for students admin number, i have included it as an identitifier to only allow students with the student ID to enter 
	if (isset($_SESSION["username"]) && preg_match("/^[2]\d{6,6}[A-Z]$/", $_SESSION["admin_number"]) == 1)
{

	$id = $_SESSION['id'];
    
    $con = mysqli_connect("localhost", "root", "", "crontrack_db");
    
    $query = "SELECT id, username, password, nric, full_name, date_of_birth, gender, home_address, email_address, contact_number, account_active_till FROM crontrack_db.users WHERE id = ?";
    $pQuery = $con->prepare($query);
    $pQuery->bind_param('i', $id); //bind the parameters
    $result=$pQuery->execute();
    $result=$pQuery->get_result();
    $nrows=$result->num_rows; 

	$query2 = "SELECT id, admin_number, school_of, diploma, teacher_in_charge_id FROM crontrack_db.students WHERE user_id = ?";
    $pQuery2 = $con->prepare($query2);
    $pQuery2->bind_param('i', $id); //bind the parameters
    $result2=$pQuery2->execute();
    $result2=$pQuery2->get_result();
    $nrows2=$result2->num_rows; 

	//I have pulled both tables related to the student and stored them into variables, in order for convenience sake when implementing 

            
    if ($row=$result->fetch_assoc()) {

		$userID = $row['id'];
		$userUsername =  $row['username'];
		$userNric =  $row['nric'];
		$userFullname =  $row['full_name'];
		$userDOB =  $row['date_of_birth'];
		$userGender =  $row['gender'];
		$userHomeaddress=  $row['home_address'];
		$userEmailAddress =  $row['email_address'];
		$userContact =  $row['contact_number'];


	}

	if ($row=$result2->fetch_assoc()) {

		$studentId = $row['id'];
		$studentAdmin = $row['admin_number'];
		$studentSchool = $row['school_of'];
		$studentDiploma = $row['diploma'];
		$teacher_in_charge_id = $row['teacher_in_charge_id'];

	}

	//
	//STARTING SECTOR OF YOUR CODE (FUNCTIONAILITIES)
	//

		echo "<b>From the 'user' databse you can use these variables to complete the functions you need</b>";
		echo "<br>";
		echo "User ID: " . $userID;
		echo "<br>";
		echo "Username: " . $userUsername;
		echo "<br>";
		echo "NRIC: " . $userNric;
		echo "<br>";
		echo "Fullname: " . $userFullname;
		echo "<br>";
		echo "DOB: " . $userDOB;
		echo "<br>";
		echo "Gender: " . $userGender;
		echo "<br>";
		echo "Homeaddress: " . $userHomeaddress;
		echo "<br>";
		echo "Email: " . $userEmailAddress;
		echo "<br>";
		echo "Contact: " . $userContact;
		echo "<br>";

		echo "<b>From the 'students' databse you can use these variables to complete the functions you need</b>";
		echo "<br>";
		echo "Student ID: " . $studentId;
		echo "<br>";
		echo "Admin Number: " . $studentAdmin;
		echo "<br>";
		echo "School: " . $studentSchool;
		echo "<br>";
		echo "Diploma: " . $studentDiploma;
		echo "<br>";
		echo "The teacher in charge ID: " . $teacher_in_charge_id;

	//
	//ENDING SECTOR OF YOUR CODE (FUNCTIONAILITIES)
	//

?>
<!-- With the inlcusion of login, i have also added a edit account feature based on their id, they're only allowed to edit specific fields -->
<form action="student_edit.php"><input type="submit" value="Edit Account" /></form>
<form action="logout_student.php"><input type="submit" value="Logout" /></form> 

<?php
}
else {
	
	echo "<h3>Session Forbidden</h3>";
    echo "<button class='button' role='button'><a href=login_input_student.php style='color:black; text-decoration:none;'>Go Back</a></button>";
	session_destroy();
	die("");
	
}

} else {
	echo "<h3>Session Forbidden</h3>";
    echo "<button class='button' role='button'><a href=login_input_student.php style='color:black; text-decoration:none;'>Go Back</a></button>";
	session_destroy();
	die("");
}


if (isset($_SESSION['admin_number']) && (time() - $_SESSION['time'] > $inactive)) {
    session_destroy();
	header("location:login_input_student.php");
} 

$_SESSION['time'] = time(); // Update session

?>



