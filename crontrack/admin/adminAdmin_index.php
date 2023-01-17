<?php
$inactive = 500; //This is the timer, if the user does not do anything for 8 minutes(500 seconds) it will kick them out and they will hace to login again.
ini_set('session.gc_maxlifetime', $inactive);
session_start();

if (isset($_SESSION["admin_id"])){
//Since we have already assigned a fixed pattern for admin id, i have included it as an identitifier to only allow students with the student ID to enter 
	if (isset($_SESSION["username"]) && preg_match("/^[7]\d{6,6}[A-Z]$/", $_SESSION["admin_id"]) == 1)
{

?>

<html>

    <body>

    <b>Create</b>

    <p><b>Password Creation:</b> Ensure string has two uppercase letters, has one special case letter, has two digits, string has three lowercase letters and a minimum of 8 characters</p>
        
    <form action="adminAdmin_index.php" method="post">

    <table>
        
        <tr>
            <td>Username:</td>
            <td><input type="text" name="username"></td>
        </tr>

        <tr>
            <td>Password:</td>
            <td><input type="password" name="password"></td>
        </tr>

        <tr>
            <td>Admin Number:</td>
            <td><input type="text" name="admin_id"></td>
        </tr>


    </table>

    <input type="submit" value="Submit" name="Submit">
    </form>

    </body>

</html>

<?php



$con = mysqli_connect("localhost", "root", "", "crontrack_admin_db");

$query = "SELECT id, admin_id, username, password FROM crontrack_admin_db.admin";

$result = mysqli_query($con,$query);

$nrows = mysqli_num_rows($result);


if ($nrows>0) {
    echo "<table border='1' table class='table table-striped'><tr>";
            echo "<th>Id</th>";
            echo "<th>Admin No:</th>";
            echo "<th>Username:</th>";
            echo "<th>Password:</th>";
        echo "</tr>";
        while ($row=mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>";
            echo $row['id'];
            echo "</td>";
            echo "<td>";
            echo $row['admin_id'];
            echo "</td>";
            echo "<td>";
            echo $row['username'];
            echo "</td>";
            echo "<td>";
            echo $row['password'];
            echo "</td>";
            echo "<td>";
            echo "<a href='adminAdmin_edit.php?Submit=Edit&id=".$row['id']."'>Edit</a>";
            echo "</td>";
            echo "<td>";
            echo "<a href='adminAdmin_index.php?Submit=Delete&id=".$row['id']."'>Delete</a>";
            echo "</td>";
            echo "</tr>";
    
        }
        echo "</table>";
        
    }
    else {
        echo "0 records<br>";
    }

if(isset($_POST['Submit'])){

if (!empty($_POST['admin_id'])  && !empty($_POST['username'])  && !empty($_POST['password'])){

        $admin_id = $_POST['admin_id'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $checker = 0;
        $checker1 = 0;
        
        $adminId_Checker = preg_match("/^[7]\d{6,6}[A-Z]$/",$admin_id);
        $passwordChecker = preg_match("/^(?=.*[A-Z].*[A-Z])(?=.*[!@#$&*])(?=.*[0-9].*[0-9])(?=.*[a-z].*[a-z].*[a-z]).{8,20}$/",$password);


        if ($passwordChecker == 1){
            $checker = 1;
        } else {
            echo "<p style='color:red;'>* Pls enter a valid password at least 8 characters long\n</p>";
            $checker = 0;
        }

        if ($adminId_Checker == 1){
            $checker1 = 1;
        } else {
            echo "<p style='color:red;'>* Pls enter a valid Admin Number long\n</p>";
            $checker1 = 0;
        }

        
        
        if ($checker == 1 && $checker == 1){
            $query = $con->prepare("INSERT INTO `admin` (`admin_id`,`username`,`password`) VALUES (?,?,?)");
        
            $query->bind_param('sss' , $admin_id, $username, $password_hash);
        
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
        }

    } else {
        echo "<p style='color:red;'>* A reminder that all fields should be answered!\n</p>";
    } 
} 



    
   
        
  if(isset($_GET['Submit']) && $_GET['Submit'] === "Delete"){ 

    $id = $_GET['id'];
    $query= $con->prepare("Delete from admin where id = ?");
    $query->bind_param('i', $id); //bind the parameters

    header("location: adminAdmin_index.php");
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