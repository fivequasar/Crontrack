<html>
<?php 
session_start();
session_destroy();
?>
<head>
</head>

<body>
<p>Welcome to the Staff Portal</p>
<form method="post" action="login_process_admin.php">
<input type="text" name="username" placeholder="Username">
<input type="text" name="password" placeholder="Password">
<button>Sign in</button>
</form>

</body>
</html>