<?php
$inactive = 500; //This is the timer, if the user does not do anything for 8 minutes(500 seconds) it will kick them out and they will hace to login again.
ini_set('session.gc_maxlifetime', $inactive);
session_start();

if (isset($_SESSION["admin_id"])){
//Since we have already assigned a fixed pattern for teacher admin number, i have included it as an identitifier to only allow students with the student ID to enter 
	if (isset($_SESSION["username"]) && preg_match("/^[7]\d{6,6}[A-Z]$/", $_SESSION["admin_id"]) == 1)
{

?>

<?php

$con = mysqli_connect("localhost", "root", "", "crontrack_db");

$query = "SELECT id, username FROM crontrack_db.users";

$result = mysqli_query($con,$query);

$nrows = mysqli_num_rows($result); 

$query1 = "SELECT id, user_id, job_position, staff_number, school_of FROM crontrack_db.teachers";

$result1 = mysqli_query($con,$query1);

$nrows1 = mysqli_num_rows($result1); 


?>

<b>Create</b><br>


<form action="adminTeacher_index.php" method="post">

<p>*Staff ID will have the first character as '1' and will have a maximum of 8 characers with an alphabet at the end</p>
<p>*School of will only have a maxmium of 3 characters (all alphabets strictly NO intergers)</p>


<?php

if ($nrows>0) {
     echo "<label for='user_id'>ID/Username:  </label>";
     echo "<select name='user_id'>";
        while ($row=mysqli_fetch_assoc($result)) { 
            echo "<option value='";
            echo $row['id'];
            echo "'>";
            echo $row['id'];
            echo ' - ';
            echo $row['username'];
            echo "</option>";
        }
        echo "</select>";
    }
    else {
        header('location:index_admin.php');
    }
?>


<label for="job_position">Job Position:</label>
  <select name="job_position" >
    <option value="Office Staff">Office Staff</option>
    <option value="Teacher">Teacher</option>
    <option value="Principal">Principal</option>
  </select>
  
<table>
        <tr>
            <td>Staff ID: </td>

            <td><input type="text" name="staff_number"></td>
            
        </tr>
        <tr>
            <tr>
            <td>School Of:</td>
            <td>
            <select name="school_of"> 
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

if ($nrows1>0) {
    echo "<table border='1' table class='table table-striped'><tr>";
            echo "<th>Id</th>";
            echo "<th>User ID</th>";
            echo "<th>Job Position</th>";
            echo "<th>Staff ID</th>";
            echo "<th>School Of</th>";
        echo "</tr>";
        while ($row=mysqli_fetch_assoc($result1)) {
            echo "<tr>";
            echo "<td>";
            echo $row['id'];
            echo "</td>";
            echo "<td>";
            echo $row['user_id'];
            echo "</td>";
            echo "<td>";
            echo $row['job_position'];
            echo "</td>";
            echo "<td>";
            echo $row['staff_number'];
            echo "</td>";
            echo "<td>";
            echo $row['school_of'];
            echo "</td>";
            echo "<td>";
            echo "<a href='adminTeacher_edit.php?Submit=Edit&id=".$row['id']."'>Edit</a>";
            echo "</td>";
            echo "<td>";
            echo "<a href='adminTeacher_index.php?Submit=Delete&id=".$row['id']."'>Delete</a>";
            echo "</td>";
            echo "</tr>";
    
        }
        echo "</table>";
        
    }
    else{
      echo "0 records";
        
    }

    if(isset($_POST['Submit']) && !empty($_POST['Submit'])){

        if (!empty($_POST['user_id']) && !empty($_POST['job_position']) && !empty($_POST['staff_number'])  && !empty($_POST['school_of'])){

        $checker0 = 0;
        $checker1 = 0;

        $user_id =  $_POST['user_id'];
        $job_position = $_POST['job_position'];
        $staff_number = $_POST['staff_number'];
        $school_of = $_POST['school_of'];

        $staffId_Checker = preg_match("/^[1]\d{6,6}[A-Z]$/",$staff_number);

        $schoolOf_checker = preg_match("/^[A-Z]{3,3}$/",$school_of);

            if ($staffId_Checker == 1){
                $checker0 = 1;
            } else {
                echo "<p style='color:red;'>* Pls enter a valid Staff ID\n</p>";
                $checker0 = 0;
            }

            if ($schoolOf_checker == 1){
                $checker1 = 1;
            } else {
                echo "<p style='color:red;'>* Pls enter a valid school acryonym (3 characters only)\n</p>";
                $checker1 = 0;
            }

            if($checker0 == 1 && $checker1 == 1){

                $query = $con->prepare("INSERT INTO `teachers` (`user_id`,`job_position`,`staff_number`,`school_of`) VALUES (?,?,?,?)");
    
                $query->bind_param('isss' , $user_id, $job_position, $staff_number, $school_of);
            
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
            
                $con->close();
            }

        } else {
            echo "&nbsp;";
            echo "<br>";
            echo "Make sure no field is left empty";
            echo "<br>";
            echo "&nbsp;";
        }

    } 

    if(isset($_GET['Submit']) && $_GET['Submit'] === "Delete"){ 

        $id = $_GET['id'];
        $query= $con->prepare("Delete from teachers where id = ?");
        $query->bind_param('i', $id);
    
        header("location: adminTeacher_index.php");
        $query->execute();
    
    }

?>
&nbsp;
<br>
<a href="index_admin.php"><button>Back</button></a>
<br>
&nbsp;
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