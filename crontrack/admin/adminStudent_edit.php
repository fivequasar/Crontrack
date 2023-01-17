
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

$query = "SELECT id, user_id, admin_number, school_of, diploma, teacher_in_charge_id FROM crontrack_db.students WHERE id = ?";

$pQuery = $con->prepare($query);

$pQuery->bind_param('i', $id); //bind the parameters

$result=$pQuery->execute();

$result=$pQuery->get_result();

$nrows=$result->num_rows; 

$query1 = "SELECT id, user_id, job_position, staff_number, school_of FROM crontrack_db.teachers";

$result1 = mysqli_query($con,$query1);

$nrows1 = mysqli_num_rows($result1); 


        
if ($row=$result->fetch_assoc()) {

    $teacher_id = $row['teacher_in_charge_id'];
    ?> 
    
<b>Update</b><br>

<form action="adminStudent_edit.php" method="post">

<table>
        <tr>
            <td>Admin Number:</td>
            <td><input type="text" name="admin_number" value="<?php echo $row['admin_number']?>"></td>
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

        <tr>
            <td>Diploma:</td>
            <td>
                <input type="text" name="diploma" value="<?php echo $row['diploma']?>">
            </td>
        </tr>

        <input type="hidden" name="id" value="<?php echo $row['id'];?>">

        <?php

        if ($nrows1>0) {
            echo "<label for='teacher_in_charge_id'>Teacher In Charge ID: </label>";
            echo "<select name='teacher_in_charge_id'>";
            echo "<option value='$teacher_id' selected>$teacher_id </option>";
                while ($row=mysqli_fetch_assoc($result1)) {
                    
                    echo "<option value='";
                    echo $row['id'];
                    echo "'>";
                    echo $row['id'];
                    echo ' - ';
                    echo $row['staff_number'];
                    echo "</option>";
                }
                echo "</select>";
                echo "<br>";
            }
        }
        ?>

</table>

<input type="submit" name="Submit">

</form>

<?php  

if(isset($_POST['Submit'])){

    if (!empty($_POST['id']) && !empty($_POST['admin_number']) && !empty($_POST['school_of'])  && !empty($_POST['diploma']) && !empty($_POST['teacher_in_charge_id'])){

        $id = $_POST['id'];
        $admin_number = $_POST['admin_number'];
        $school_of = $_POST['school_of'];
        $diploma = $_POST['diploma'];
        $teacher_in_charge_id = $_POST['teacher_in_charge_id'];
    
        $adminId_Checker = preg_match("/^[2]\d{6,6}[A-Z]$/",$admin_number);
    
        $schoolOf_checker = preg_match("/^[A-Z]{3,3}$/",$school_of);
    
        $diploma_checker = preg_match("/^[A-Za-z ]+$/",$diploma);
    
        if ($adminId_Checker == 1){
            $checker0 = 1;
        } else {
            echo "<p style='color:red;'>* Ensure that your Admin Number is Valid\n</p>";
            $checker0 = 0;
        }
    
        if ($schoolOf_checker == 1){
            $checker1 = 1;
        } else {
            echo "<p style='color:red;'>* Pls enter a valid school acryonym (3 characters only)\n</p>";
            $checker1 = 0;
        }
    
        if ($diploma_checker == 1){
            $checker2 = 1;
        } else {
            echo "<p style='color:red;'>* Pls enter a valid Diploma\n</p>";
            $checker2 = 0;
        }

        if($checker0 == 1 && $checker1 == 1 && $checker2 == 1){

            $query= $con->prepare("UPDATE students set admin_number=?, school_of=?, diploma=?, teacher_in_charge_id=? WHERE id=?");
            $query->bind_param('sssii' , $admin_number, $school_of, $diploma, $teacher_in_charge_id, $id);
    
            try {
                $query->execute();
            } catch (Throwable $e) {
                $value = 1;  
            }
    
            if ($value == 1){
                echo "Duplicated Entry Found!";
            } elseif ($value == 0){
                header("location: adminStudent_index.php");
            }

        } else  {
            echo "&nbsp;";
            echo "<br>";
            echo "<a href=\"javascript:history.go(-1)\"><button class='button' role='button'>Go Back</button></a>";
            echo "<br>";
            echo "&nbsp;";
        }
    
    } else {
            error_reporting(E_ERROR | E_PARSE);
            echo "<p>All fields must not be empty!</p>";
            echo "<br>";
            echo "<a href=\"javascript:history.go(-1)\"><button class='button' role='button'>Go Back</button></a>";
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