<?php
$inactive = 500; 
ini_set('session.gc_maxlifetime', $inactive);
session_start();

if (isset($_SESSION["staff_number"])){

	if (isset($_SESSION["username"]) && preg_match("/^[1]\d{6,6}[A-Z]$/", $_SESSION["staff_number"]) == 1)
{
	$id = $_SESSION['id'];
    
    $con = mysqli_connect("localhost", "root", "", "crontrack_db");
    
    $query = "SELECT id, username, password, nric, full_name, date_of_birth, gender, home_address, email_address, contact_number, account_active_till FROM crontrack_db.users WHERE id = ?";
    $pQuery = $con->prepare($query);
    $pQuery->bind_param('i', $id); //bind the parameters
    $result=$pQuery->execute();
    $result=$pQuery->get_result();
    $nrows=$result->num_rows; 

	$query2 = "SELECT id, job_position, staff_number, school_of FROM crontrack_db.teachers WHERE user_id = ?";
    $pQuery2 = $con->prepare($query2);
    $pQuery2->bind_param('i', $id); //bind the parameters
    $result2=$pQuery2->execute();
    $result2=$pQuery2->get_result();
    $nrows2=$result2->num_rows; 

	
            
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

		$staffId = $row['id'];
		$staffJob = $row['job_position'];
		$staff_admin_id = $row['staff_number'];
		$staffSchool = $row['school_of'];

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
	echo "Student ID: " . $staffId;
	echo "<br>";
	echo "Admin Number: " . $staffJob;
	echo "<br>";
	echo "School: " . $staff_admin_id;
	echo "<br>";
	echo "Diploma: " . $staffSchool;
	echo "<br>";

//
//ENDING SECTOR OF YOUR CODE (FUNCTIONAILITIES)
//

?>

<form action="teachers_edit.php"><input type="submit" value="Edit Account" /></form>
<form action="logout_teachers.php"><input type="submit" value="Logout" /></form> 

<?php


}
else {
	echo "<h3>Session Forbidden</h3>";
    echo "<button class='button' role='button'><a href=login_input_teachers.php>Go Back</a></button>";
	session_destroy();
	die("");
}

} else {
	echo "<h3>Session Forbidden</h3>";
    echo "<button class='button' role='button'><a href=login_input_teachers.php style='color:black; text-decoration:none;'>Go Back</a></button>";
	session_destroy();
	die("");
}

if (isset($_SESSION['staff_number']) && (time() - $_SESSION['time'] > $inactive)) {
    session_destroy();
	header("location:login_input_teachers.php");
} 

$_SESSION['time'] = time(); // Update session




?>


