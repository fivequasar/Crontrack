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

$con = mysqli_connect("localhost", "root", "", "crontrack_admin_db");

$query = "SELECT id, admin_id, username, password FROM crontrack_admin_db.admin WHERE id = ?";

$pQuery = $con->prepare($query);

$pQuery->bind_param('i', $id);

$result=$pQuery->execute();

$result=$pQuery->get_result();

$nrows=$result->num_rows; 

if ($row=$result->fetch_assoc()) {

    ?>

<b>Update</b><br>

<p><b>Password Update:</b> Ensure string has two uppercase letters, has one special case letter, has two digits, string has three lowercase letters and a minimum of 8 characters</p>
<p>*Admin ID will have the first character as '7' and will have a maximum of 8 characers with an alphabet at the end</p>   




<form action="adminAdmin_edit.php?Submit=Edit&id="<?php $row['id'] ?> method="post">

<table>
        <tr>
            <td>Admin Number:</td>
            <td><input type="text" name="admin_id" value="<?php echo $row['admin_id']?>"></td>
        </tr>

        <tr>
            <td>Username:</td>
            <td><input type="text" name="username" value="<?php echo $row['username']?>"></td>
        </tr>

        <tr>
            <td>Password:</td>
            <td><input type="text" name="password"></td>
        </tr>

        <input type="hidden" name="id" value="<?php echo $row['id'];?>">

        </table>

        <input type="submit" name="Submit">

</form>

        <?php


}

if(isset($_POST['Submit'])){

if (!empty($_POST['admin_id'])  && !empty($_POST['username'])  && !empty($_POST['password'])){

        $admin_id = $_POST['admin_id'];
        $id = $_POST['id'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $checker = 0;
        $checker1 = 0;
        
        $adminId_Checker = preg_match("/^[7]\d{6,6}[A-Z]$/",$admin_id);
        $passwordChecker = preg_match("/^(?=.*[A-Z].*[A-Z])(?=.*[!@#$&*])(?=.*[0-9].*[0-9])(?=.*[a-z].*[a-z].*[a-z]).{8,20}$/",$password);


        if ($passwordChecker == 1){
            $checker = 1;
        } 

        if ($adminId_Checker == 1){
            $checker1 = 1;
        } 

        
        
        if ($checker == 1 && $checker1 == 1){
            $query = $con->prepare("UPDATE admin set admin_id=?, username=?, password=? WHERE id=?");

            $query->bind_param('sssi' , $admin_id, $username, $password_hash, $id);
        
            try {
                $query->execute();
            } catch (Throwable $e) {
                $value = 1;  
            }

            if ($value == 1){
                echo "Duplicated Entry Found!";
            } elseif ($value == 0){
                header("location: adminAdmin_index.php");
            }
        
            
        
            $con->close();
        } elseif ($checker == 0) {
            echo "<p style='color:red;'>* Pls enter a valid password at least 8 characters long\n</p>";
            echo "<button class='button' role='button'><a href=\"javascript:history.go(-1)\" style='color:black; text-decoration:none;'>Go Back</a></button>";
        } elseif ($checker1 == 0){
            echo "<p style='color:red;'>* Pls enter a valid Admin Number long\n</p>";
            echo "<button class='button' role='button'><a href=\"javascript:history.go(-1)\" style='color:black; text-decoration:none;'>Go Back</a></button>";
        }

    } else {
        echo "<p style='color:red;'>* A reminder that all fields should be answered!\n</p>";
        echo "<button class='button' role='button'><a href=\"javascript:history.go(-1)\" style='color:black; text-decoration:none;'>Go Back</a></button>";
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