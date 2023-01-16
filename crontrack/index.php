<p>
<form action="index.php">
<select name="value">
  <option value="Teacher">Teacher</option>
  <option value="Student">Student</option>
  <option value="Admin">Staff</option>
</select>
<input type="submit" value="submit" name="submit">

</form>


</p>

<?php

if (isset($_GET["submit"])){

    $link_value = $_GET["value"];

    if ($link_value == 'Student'){
        header('Location: student/login_input_student.php');
    } elseif ($link_value == 'Teacher') {
        header('Location: teachers/login_input_teachers.php');
    } elseif ($link_value == 'Admin') {
        header('Location: admin/login_input_admin.php');
    } 
}

?>