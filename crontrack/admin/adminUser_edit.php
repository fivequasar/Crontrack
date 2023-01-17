<?php
$inactive = 500; //This is the timer, if the user does not do anything for 8 minutes(500 seconds) it will kick them out and they will hace to login again.
ini_set('session.gc_maxlifetime', $inactive);
session_start();

if (isset($_SESSION["admin_id"])){
//Since we have already assigned a fixed pattern for students admin number, i have included it as an identitifier to only allow students with the student ID to enter 
	if (isset($_SESSION["username"]) && preg_match("/^[7]\d{6,6}[A-Z]$/", $_SESSION["admin_id"]) == 1)
{

?>

<style>

.button a {
    text-decoration: none;
    color: black;
}

</style>

<?php



error_reporting(E_ERROR | E_PARSE);

$id = $_GET['id'];

$con = mysqli_connect("localhost", "root", "", "crontrack_db");

$query = "SELECT id, username, password, nric, full_name, date_of_birth, gender, home_address, email_address, contact_number, account_active_till FROM crontrack_db.users WHERE id = ?";

$pQuery = $con->prepare($query);

$pQuery->bind_param('i', $id); //bind the parameters

$result=$pQuery->execute();

$result=$pQuery->get_result();

$nrows=$result->num_rows; 
        
if ($row=$result->fetch_assoc()) {

    ?>
    
    <b>Update</b><br>

                <form action="adminUser_edit.php" method="post">

                    <table>

                    <tr><td>Username:</td><td><input type="text" name="username" value="<?php echo $row['username']?>"></td></tr>

                    <tr><td>Password:</td><td><input type="password" name="password"></td></tr>

                    <tr><td></td><td style="color:red;">* Pls re-enter the password again.</td></tr>

                    <tr><td>NRIC:</td><td><input type="text" name="nric" value="<?php echo $row['nric']?>"></td></tr>

                    <tr><td>Full Name:</td><td><input type="text" name="full_name" value="<?php echo $row['full_name']?>"></td></tr>

                    <tr><td>Date of Birth: </td><td><input type="date" name="date_of_birth" value="<?php echo $row['date_of_birth']?>" max="2005-12-12"></td></tr>


                    <tr>
                        <td>Gender:</td>
                        <td>
                            <input type="radio" name="gender" value="<?php echo $row['gender']?>" checked><label><?php echo $row['gender']?></label>
                            <input type="radio" name="gender" value="M"><label>Male</label>
                            <input type="radio" name="gender" value="F"><label>Female</label>
                        </td>
                    </tr>
                    
                    <tr><td>Home Address:</td><td><input type="text" name="home_address" value="<?php echo$row['home_address']?>"></td></tr>

                    <tr><td>Email Address:</td><td><input type="email" name="email_address" value="<?php echo$row['email_address']?>"></td></tr>

                    <tr><td>Contact Number:</td><td><input type="text" name="contact_number" value="<?php echo $row['contact_number']?>"></td></tr>

                    <tr><td>Account Active Till: </td><td><input type="date" name="account_active_till" value="<?php echo $row['account_active_till']?>" min="<?php echo date("Y-m-d"); ?>"></td></tr>
                    

                    <tr><td></td>
                    <td>
                    <input type="hidden" name="id" value="<?php echo $row['id']?>">
                    <input type="submit" name="Submit" value="Update"></td></tr>
                    </table>
                </form>
    
    <?php 
}


if(isset($_POST['Submit'])){

    if (!empty($_POST['username']) && !empty($_POST['password'])  && !empty($_POST['nric'])  && !empty($_POST['full_name']) && !empty($_POST['date_of_birth']) && !empty($_POST['gender']) && !empty($_POST['home_address']) && !empty($_POST['email_address']) && !empty($_POST['contact_number']) && !empty($_POST['account_active_till'])){


    $id = $_POST['id'];

    $username = $_POST['username'];

    $password = $_POST['password'];
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    
    $nric = $_POST['nric'];
    $full_name = $_POST['full_name'];
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $home_address = $_POST['home_address'];
    $email_address = $_POST['email_address'];
    $contact_number = $_POST['contact_number'];
    $account_active_till = $_POST['account_active_till'];

    $checker0 = 0;
    $checker1 = 0;
    $checker2 = 0;
    $checker3 = 0;
    $checker4 = 0;
    $checker5 = 0;
    $checker6 = 0;

    $usernameChecker = preg_match('/^[A-Za-z0-9 ]*$/',$username);

    $passwordChecker = preg_match("/^(?=.*[A-Z].*[A-Z])(?=.*[!@#$&*])(?=.*[0-9].*[0-9])(?=.*[a-z].*[a-z].*[a-z]).{8,20}$/",$password);

    $nricChecker = preg_match("/^[STFG]\d{7}[A-Z]$/", $nric);

    $fullNameChecker = preg_match('/^[A-Za-z ]+$/',$full_name);

    $addressChecker = preg_match('/^[A-Za-z0-9 ]*$/',$home_address);

    $emailChecker = preg_match("/[@]/",$email_address);

    $numberChecker = preg_match("/(6|8|9)\d{7}/", $contact_number);
    

    if ($nricChecker == 1){
        $checker0 = 1;
    } else {
        echo "<p style='color:red;'>* Pls enter a valid NRIC\n</p>";
        $checker0 = 0;
    }

    if ($emailChecker == 1){
        $checker1 = 1;
    } else {
        echo "<p style='color:red;'>* Pls enter a valid email\n</p>";
        $checker1 = 0;
    }

    if ($numberChecker == 1){
        $checker2 = 1;
    } else {
        echo "<p style='color:red;'>* Pls enter a valid Singapore number\n</p>";
        $checker2 = 0;
    }

    if ($passwordChecker == 1){
        $checker3 = 1;
    } else {
        echo "<p style='color:red;'>* Pls enter a valid password at least 8 characters long\n</p>";
        $checker3 = 0;
    }

    if ($fullNameChecker == 1){
        $checker4 = 1;
    } else {
        echo "<p style='color:red;'>Ensure the full name contains only alphabets\n</p>";
        $checker4 = 0;
    }

    if ($addressChecker == 1){
        $checker5 = 1;
    } else {
        echo "<p style='color:red;'>Ensure the full address contains only alphabets and numbers\n</p>";
        $checker5 = 0;
    }

    if ($usernameChecker == 1){
        $checker6 = 1;
    } else {
        echo "<p style='color:red;'>Ensure the username contains only alphabets\n</p>";
        $checker6 = 0;
    }
    
    if ($checker0 == 1 && $checker1 == 1 && $checker2 == 1 && $checker3 == 1 && $checker4 == 1 && $checker5 == 1 && $checker6 == 1){
        
        $query= $con->prepare("UPDATE users set username=?, password=?, nric = ?, full_name=?, date_of_birth=?, gender=?, home_address=?, email_address=?, contact_number=?, account_active_till=? WHERE id=?");
        $query->bind_param('ssssssssisi' , $username, $password_hash, $nric, $full_name, $date_of_birth, $gender, $home_address, $email_address, $contact_number, $account_active_till, $id);

        try {
            $query->execute();
        } catch (Throwable $e) {
            $value = 1;  
        }

        if ($value == 1){
            echo "Duplicated Entry Found!";
        } elseif ($value == 0){
            header("location: adminUser_index.php");
        }
        
    } else {
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
$con->close();


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

