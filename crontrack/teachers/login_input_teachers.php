<html>
<?php 
session_start();
session_destroy();
?>
<head>
</head>

<body>
<p>Welcome to the Teachers Portal</p>
<form method="post" action="login_process_teachers.php">
<input type="text" name="username" placeholder="Username">
<input type="text" name="password" placeholder="Password">
<button>Sign in</button>
</form>

</body>
</html>
