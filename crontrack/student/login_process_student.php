<?php	

//This is to check whether the fields have data in it
$checkall=true;
$checkall=$checkall && checkpost("username", true, "/^[a-zA-Z0-9]+$/");
$checkall=$checkall && checkpost("password", true, "/^[a-zA-Z0-9!@#$%^&*]+$/");

if (!$checkall) {
	echo "Login Error";
	die();
} else { 
	logindo($_POST["username"], $_POST["password"]);
}


// return true if checks ok
function checkpost($input, $mandatory, $pattern) {

	$inputvalue=$_POST[$input];

	if (empty($inputvalue)) {
		echo "$input field is empty<br>";
		if ($mandatory) return false;
		else echo "but $input is not mandatory";
	}
	if (strlen($pattern) > 0) {
		$ismatch=preg_match($pattern,$inputvalue);
		if (!$ismatch || $ismatch==0) {
			echo "$input field wrong format <br>";
			if ($mandatory) return false;
		}
	}
	return true;
}





function logindo($username, $password) {
    // check if the user has already attempted to login 3 times
    if(isset($_COOKIE['attempts'])){
        $attempts = $_COOKIE['attempts'];
        if ($attempts >= 3) {
            echo "You have reached the maximum number of login attempts. Please wait 1 minute to try again.";
			echo "<br>";
			echo "&nbsp;";
			echo "<br>";
			echo "<a href=\"login_input_student.php\"><button class='button' role='button'>Go Back</button></a>";
            exit;
        }
    }

    $db_hostname="127.0.0.1";
    $db_username="root";
    $db_password="";
    $db_database="crontrack_db";

    $con=mysqli_connect($db_hostname,$db_username,$db_password);
    $result=mysqli_select_db($con, $db_database);
    $query="SELECT id, username, password, full_name, gender, home_address, email_address, contact_number, account_active_till FROM crontrack_db.users WHERE username='$username' ";
    $result=mysqli_query($con,$query);
    $nrows=mysqli_num_rows($result);
    $data=mysqli_fetch_all($result,MYSQLI_ASSOC);

    if ($nrows == 1){

    session_start();

    $hash = $data[0]["password"];

    if (password_verify($password, $hash)){ 

    $_SESSION["username"]=$data[0]["username"]; 
    $_SESSION["password"]=$data[0]["password"]; 
    $_SESSION["id"]=$data[0]["id"];
    $id_students = $_SESSION["id"];
    $_SESSION['time'] = time();
    $query2="SELECT id, user_id, admin_number, school_of, diploma, teacher_in_charge_id FROM crontrack_db.students WHERE user_id = $id_students";

    $result2=mysqli_query($con,$query2);
    $nrows2=mysqli_num_rows($result2);
    $data2=mysqli_fetch_all($result2,MYSQLI_ASSOC);

    if ($nrows2 == 1){
        $_SESSION["admin_number"]=$data2[0]["admin_number"]; 
        $_SESSION["teacher_in_charge_id"]=$data2[0]["teacher_in_charge_id"]; 

    } elseif ($nrows2 == 0){
        echo "This user has not been assigned a student";
    }

    setcookie("colour", "red", time()+30*24*60*60, "/"); 
    setcookie("weather","good", time()+30*24*60*60, "/");
    setcookie("attempts", 0, time()+1*60, "/"); // reset the attempts cookie

    print_r($_SESSION);

    mysqli_free_result($result);

    mysqli_close($con);

    header('Location: index_student.php');

} else {
    echo "Login Error!";
    echo "<br>";
    echo "&nbsp";
    echo "<br>";
    if(isset($_COOKIE['attempts'])){
        $attempts = $_COOKIE['attempts'] + 1;
    } else {
        $attempts = 1;
    }
    setcookie("attempts", $attempts, time()+1*60, "/");
    echo "<a href=\"javascript:history.go(-1)\"><button class='button' role='button'>Go Back</button></a>";
}

} elseif ($nrows == 0) {
    echo "Login Error!";
    echo "<br>";
    echo "&nbsp";
    echo "<br>";
    if(isset($_COOKIE['attempts'])){
        $attempts = $_COOKIE['attempts'] + 1;
    } else {
        $attempts = 1;
    }
    setcookie("attempts", $attempts, time()+1*60, "/");
    echo "<a href=\"javascript:history.go(-1)\"><button class='button' role='button'>Go Back</button></a>";
}
}

?>
