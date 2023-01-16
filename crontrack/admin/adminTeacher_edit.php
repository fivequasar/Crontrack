<?php
$inactive = 500; //This is the timer, if the user does not do anything for 8 minutes(500 seconds) it will kick them out and they will hace to login again.
ini_set('session.gc_maxlifetime', $inactive);
session_start();

if (isset($_SESSION["admin_id"])){
//Since we have already assigned a fixed pattern for students admin number, i have included it as an identitifier to only allow students with the student ID to enter 
	if (isset($_SESSION["username"]) && preg_match("/^[7]\d{6,6}[A-Z]$/", $_SESSION["admin_id"]) == 1)
{

?>


<?php
    
    error_reporting(E_ERROR | E_PARSE);

    $id = $_GET['id'];

    $con = mysqli_connect("localhost", "root", "", "crontrack_db");

    $query = "SELECT id, user_id, job_position, staff_number, school_of FROM crontrack_db.teachers WHERE id = ?";

    $pQuery = $con->prepare($query);

    $pQuery->bind_param('i', $id); //bind the parameters

    $result=$pQuery->execute();

    $result=$pQuery->get_result();

    $nrows=$result->num_rows; 
            
    if ($row=$result->fetch_assoc()) {

?> 

<b>Update</b><br>

<form action="adminTeacher_edit.php" method="post">

<p>*Staff ID will have the first character as '1' and will have a maximum of 8 characers with an alphabet at the end</p>
<p>*School of will only have a maxmium of 3 characters (all alphabets strictly NO intergers)</p>

<label for="job_position">Job Position:</label>
  <select name="job_position" >
  <option value="<?php echo $row['job_position']?>"> Current: <?php echo $row['job_position']?></option>
    <option value="Office Staff">Office Staff</option>
    <option value="Teacher">Teacher</option>
    <option value="Principal">Principal</option>
  </select>

<table>
        <tr>
            <td>Staff ID:</td>
            <td><input type="text" name="staff_number" value="<?php echo $row['staff_number']?>"></td>
        </tr>

        <tr>
            <td>School Of:</td>
            <td>
            <select name="school_of"> 
                <option value="<?php echo $row['school_of']?>">Current School <?php echo $row['school_of']?></option>
                <option value="IIT">School of Informatics & IT</option>
                <option value="ENG">School of Engineering</option>
                <option value="BUS">School of Business</option>
                <option value="DES">School of Design</option>
            </select>
            </td>
        </tr>
        <input type="hidden" name="id" value="<?php echo $row['id']?>">
</table>

<input type="submit" name="Submit">
</form>

<?php 

}

if(isset($_POST['Submit'])){

    if (!empty($_POST['job_position']) && !empty($_POST['staff_number'])  && !empty($_POST['school_of'])){

    $checker0 = 0;
    $checker1 = 0;

    $id =  $_POST['id'];
    $job_position = $_POST['job_position'];
    $staff_number = $_POST['staff_number'];
    $school_of = $_POST['school_of'];

    $staffIdChecker = preg_match("/^[1]\d{6,6}[A-Z]$/",$staff_number);

    $schoolOfchecker = preg_match("/^[A-Z]{3,3}$/",$school_of); 

    if ($staffIdChecker == 1){
        $checker0 = 1;
    } else {
        echo "<p style='color:red;'>* Pls enter a valid Staff ID\n</p>";
        $checker0 = 0;
    }

    if ($schoolOfchecker == 1){
        $checker1 = 1;
    } else {
        echo "<p style='color:red;'>* Pls enter a valid school acryonym (3 characters only)\n</p>";
        $checker1 = 0;
    }

    if($checker0 == 1 && $checker1 == 1){

        $query= $con->prepare("UPDATE teachers set job_position=?, staff_number = ?, school_of=? WHERE id=?");
        $query->bind_param('sssi' , $job_position, $staff_number, $school_of, $id);

        try {
            $query->execute();
        } catch (Throwable $e) {
            $value = 1;  
        }

        if ($value == 1){
            echo "Duplicated Entry Found!";
        } elseif ($value == 0){
            header("location: adminTeacher_index.php");
        }

    } else {
        echo "&nbsp;";
        echo "<br>";
        echo "<a href=\"javascript:history.go(-1)\"><button class='button' role='button'>Go Back</button></a><br>";
        echo "<br>";
        echo "&nbsp;";
    }

    } else {
        echo "Make sure nothing is empty!<br>";
        echo "<br>";
        echo "<a href=\"javascript:history.go(-1)\"><button class='button' role='button'>Go Back</button></a><br>";
        echo "<br>";
        echo "&nbsp;";
    }

}

?>
<form action="logout_admin.php"><input type="submit" value="Logout" /></form> 
<?php
}
else {
	
	echo "<h3>Session Forbidden</h3>";
    echo "<button class='button' role='button'><a href=login_input_admin.php style='color:black; text-decoration:none;'>Go Back</a></button>";
	session_destroy();
	die("");
	
}

} else {
	echo "<h3>Session Forbidden</h3>";
    echo "<button class='button' role='button'><a href=login_input_admin.php style='color:black; text-decoration:none;'>Go Back</a></button>";
	session_destroy();
	die("");
}


if (isset($_SESSION['admin_id']) && (time() - $_SESSION['time'] > $inactive)) {
    session_destroy();
	header("location:login_input_admin.php");
} 

$_SESSION['time'] = time(); // Update session

?>
