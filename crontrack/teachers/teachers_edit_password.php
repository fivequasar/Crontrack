<?php
$inactive = 500; //This is the timer, if the user does not do anything for 8 minutes(500 seconds) it will kick them out and they will hace to login again.
ini_set('session.gc_maxlifetime', $inactive);
session_start();

if (isset($_SESSION["username"]) && preg_match("/^[1]\d{6,6}[A-Z]$/", $_SESSION["staff_number"]) == 1)
{
    
    error_reporting(E_ERROR | E_PARSE);
    
    $id = $_SESSION['id'];
    
    $con = mysqli_connect("localhost", "root", "", "crontrack_db");
    
    $query = "SELECT id, password FROM crontrack_db.users WHERE id = ?";
    
    $pQuery = $con->prepare($query);
    
    $pQuery->bind_param('i', $id); //bind the parameters
    
    $result=$pQuery->execute();
    
    $result=$pQuery->get_result();
    
    $nrows=$result->num_rows; 
            
    if ($row=$result->fetch_assoc()) {

        ?>
    
    <b>Update</b><br>

                <form action="teachers_edit_password.php" method="post">

                    <table>

                    <tr><td>Enter your old password:</td><td><input type="password" name="old_password"></td></tr>
                    <tr><td>Password:</td><td><input type="password" name="new_password"></td></tr>
                    
                    <tr><td></td>
                    <td>
                    <input type="hidden" name="id" value="<?php echo $row['id']?>">
                    <input type="hidden" name="password" value="<?php echo $row['password']?>">
                    <input type="submit" name="Submit" value="Update"></td></tr>
                    </table>
                </form>
    
    <?php 
}
        
if(isset($_POST['Submit'])){

    if (!empty($_POST['password']) && !empty($_POST['old_password'])){

    
    $id = $_POST['id'];
    $password = $_POST['new_password'];
    $old_password = $_POST['old_password'];
    $hash = $_POST['password'];
    
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $checker0 = 0;
    $passwordChecker = preg_match("/^(?=.*[A-Z].*[A-Z])(?=.*[!@#$&*])(?=.*[0-9].*[0-9])(?=.*[a-z].*[a-z].*[a-z]).{1,20}$/",$password);

    if ($passwordChecker == 1){
        $checker0 = 1;
    } else {
        echo "<p style='color:red;'>* Pls enter a valid Password\n</p>";
        $checker0 = 0;
    }

    if (($checker0 == 1)) {

        if (password_verify($old_password, $hash)){ 
        
        $query= $con->prepare("UPDATE users SET password=? WHERE id=?");
        $query->bind_param('si' , $password_hash, $id);

        $query->execute();  //execute query
        header("location: index_teachers.php");

    } else {
        echo "Wrong Password";
    }
    } else {
        echo "";
    }

} else {
        error_reporting(E_ERROR | E_PARSE);
        echo "All fields must not be empty\n";
        echo "<br>";
        echo "<p></p>";
}

}
$con->close();

?>
<form action="logout_teachers.php"><input type="submit" value="Logout" /></form>
<?php

}
else {

    echo "<h3>Session Forbidden</h3>";
    echo "<button class='button' role='button' <a href=login_input_student.php style='color:black; text-decoration:none;'>Go Back</a></button>";
	die("");

}

if (isset($_SESSION['staff_number']) && (time() - $_SESSION['time'] > $inactive)) {
    session_destroy();
	header("location:login_input_teachers.php");
} 

$_SESSION['time'] = time(); // Update session


?>