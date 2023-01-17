<?php
$inactive = 500; 
ini_set('session.gc_maxlifetime', $inactive);
session_start();

if (isset($_SESSION["username"]) && preg_match("/^[1]\d{6,6}[A-Z]$/", $_SESSION["staff_number"]) == 1)
{
    
    error_reporting(E_ERROR | E_PARSE);
    
    $id = $_SESSION['id'];
    
    $con = mysqli_connect("localhost", "root", "", "crontrack_db");
    
    $query = "SELECT id, username, password, home_address, email_address, contact_number FROM crontrack_db.users WHERE id = ?";
    
    $pQuery = $con->prepare($query);
    
    $pQuery->bind_param('i', $id); //bind the parameters
    
    $result=$pQuery->execute();
    
    $result=$pQuery->get_result();
    
    $nrows=$result->num_rows; 
            
    if ($row=$result->fetch_assoc()) {

        ?>
    
    <b>Update</b><br>

                <form action="teachers_edit.php" method="post">

                    <table>

                    <tr><td>Username:</td><td><input type="text" name="username" value="<?php echo $row['username']?>"></td></tr>

                    <tr><td>Home Address:</td><td><input type="text" name="home_address" value="<?php echo$row['home_address']?>"></td></tr>

                    <tr><td>Email Address:</td><td><input type="email" name="email_address" value="<?php echo$row['email_address']?>"></td></tr>

                    <tr><td>Contact Number:</td><td><input type="text" name="contact_number" value="<?php echo $row['contact_number']?>"></td></tr>
                    
                    <tr><td>Pls re-enter your password to update!:</td><td><input type="password" name="passwordInput"></td></tr>
                    
                    <tr><td></td>

                    <td>

                    <input type="hidden" name="password" value="<?php echo $row['password']?>">
                    <input type="hidden" name="id" value="<?php echo $row['id']?>">
                    <input type="submit" name="Submit" value="Update"></td></tr>
                    </table>
                </form>
    
    <?php 
}
        
if(isset($_POST['Submit'])){

    if (!empty($_POST['username']) && !empty($_POST['home_address']) && !empty($_POST['email_address']) && !empty($_POST['contact_number'])){


    $id = $_POST['id'];

    $username = $_POST['username'];
    $home_address = $_POST['home_address'];
    $email_address = $_POST['email_address'];
    $contact_number = $_POST['contact_number'];

    $password = $_POST['passwordInput'];
    $hash = $_POST['password'];

    $checker0 = 0;
    $checker1 = 0;
    $checker2 = 0;
    $checker3 = 0;

    $emailChecker = preg_match("/[@]/",$email_address);

    $numberChecker = preg_match("/^[8]|^[9]/", $contact_number);

    $usernameChecker = preg_match('/^[A-Za-z0-9 ]*$/',$username);

    $addressChecker = preg_match('/^[A-Za-z0-9 ]*$/',$home_address);

    if ($emailChecker == 1){
        $checker0 = 1;
    } else {
        echo "<p style='color:red;'>* Pls enter a valid email\n</p>";
        $checker0 = 0;
    }

    if ($numberChecker == 1 && strlen($contact_number) == 8){
        $checker1 = 1;
    } else {
        echo "<p style='color:red;'>* Pls enter a valid Singapore number\n</p>";
        $checker1 = 0;
    }

    if ($usernameChecker == 1){
        $checker2 = 1;
    } else {
        echo "<p style='color:red;'>* Pls enter a valid username\n</p>";
        $checker2 = 0;
    }

    if ($addressChecker == 1){
        $checker3 = 1;
    } else {
        echo "<p style='color:red;'>* Pls enter a valid address\n</p>";
        $checker3 = 0;
    }

    if (($checker0 == 1 && $checker1 == 1 && $checker2 == 1 && $checker3 == 1)) {

        if (password_verify($password, $hash)){
            $query= $con->prepare("UPDATE users set username=?, home_address=?, email_address=?, contact_number=? WHERE id=?");
            $query->bind_param('sssii' , $username, $home_address, $email_address, $contact_number, $id);
    
            try {
                $query->execute();
            } catch (Throwable $e) {
                $value = 1;  
            }

            if ($value == 1){
                echo "Duplicated Entry Found!";
            } elseif ($value == 0){
                header("location: index_teachers.php");
            }
            

        } else {
            echo "<p style='color:red;'>* Password is incorrect\n</p>";
        }

        
        
    } else {
    }

} else {
        error_reporting(E_ERROR | E_PARSE);
        echo "All fields must not be empty";
        
}

}
$con->close();

?>
<form action="teachers_edit_password.php"><input type="submit" value="Change Password" /></form>
<form action="logout_teachers.php"><input type="submit" value="Logout" /></form>
<?php

}
else {
	echo "<h3>Session Forbidden</h3>";
    echo "<button class='button' role='button'><a href=login_input_teachers.php style='color:black; text-decoration:none;'>Go Back</a></button>";
	die("");

}

if (isset($_SESSION['staff_number']) && (time() - $_SESSION['time'] > $inactive)) {
    session_destroy();
	header("location:login_input_teachers.php");
} 

$_SESSION['time'] = time(); // Update session



?>
