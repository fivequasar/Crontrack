<html>
<?php 
session_start();
session_destroy();
?>
<head>
</head>

<body>
<p>Welcome to the Student Portal</p>
<form method="post" action="login_process_student.php">
<input type="text" name="username" placeholder="Username">
<input type="text" name="password" placeholder="Password">
<button>Sign in</button>
</form>

</body>
</html>
