<?php
$inactive = 500; //This is the timer, if the user does not do anything for 8 minutes(500 seconds) it will kick them out and they will hace to login again.
ini_set('session.gc_maxlifetime', $inactive);
session_start();

if (isset($_SESSION["admin_id"])){
//Since we have already assigned a fixed pattern for students admin number, i have included it as an identitifier to only allow students with the student ID to enter 
	if (isset($_SESSION["username"]) && preg_match("/^[7]\d{6,6}[A-Z]$/", $_SESSION["admin_id"]) == 1)
{

    ?>

        <head>
            
            <link rel="stylesheet" href="">

            <style>

            .button a {
                text-decoration: none;
                color: black;
            }

            </style>
        </head>

        <body>

            <button class="button" role="button"><a href="adminUser_index.php">User Account Management</a></button>

            <button class="button" role="button"><a href="adminTeacher_index.php">Teacher Account Management</a></button>

            <button class="button" role="button"><a href="adminStudent_index.php">Student Account Management</a></button>

            <button class="button" role="button"><a href="adminAdmin_index.php">Admin Account Management</a></button>
            
        </body>
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