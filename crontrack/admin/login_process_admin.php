<?php	

//This is to check whether the fields have data in it
$checkall=true;
$checkall=$checkall && checkpost("username", true, "/^[a-zA-Z0-9]+$/");
$checkall=$checkall && checkpost("password",true,"");

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

	if(isset($_COOKIE['attempts'])){
        $attempts = $_COOKIE['attempts'];
        if ($attempts >= 3) {
            echo "You have reached the maximum number of login attempts. Please wait 1 minute to try again.";
			echo "<br>";
			echo "&nbsp;";
			echo "<br>";
			echo "<a href=\"login_input_admin.php\"><button class='button' role='button'>Go Back</button></a>";
            exit;
        }
    }

	$db_hostname="127.0.0.1";
	$db_username="root";
	$db_password="";
	$db_database="crontrack_admin_db";

	$con=mysqli_connect($db_hostname,$db_username,$db_password);
	$result=mysqli_select_db($con, $db_database);
	$query="SELECT id, admin_id, username, password FROM crontrack_admin_db.admin WHERE username ='$username' ";
	$result=mysqli_query($con,$query);
	$nrows=mysqli_num_rows($result);
	$data=mysqli_fetch_all($result,MYSQLI_ASSOC);

	if ($nrows == 1){

	session_start();

	$_SESSION["username"]=$data[0]["username"]; 
	$hash = $data[0]["password"];

	if (password_verify($password, $hash)){ // This password will compare the cleartext given from the user and the one from the databse 

	//From 62 to 92 will be performed if the password matches 
	$_SESSION["admin_id"]=$data[0]["admin_id"]; 
	$_SESSION["id"]=$data[0]["id"];
	$_SESSION['time'] = time();
	
	setcookie("colour", "red", time()+30*24*60*60, "/"); 
	setcookie("weather","good", time()+30*24*60*60, "/");

	print_r($_SESSION);

	echo $password;
	echo $hash;
	
	mysqli_free_result($result);

	mysqli_close($con);
	
	header('Location: index_admin.php');

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
