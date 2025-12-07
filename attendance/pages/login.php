<?php
session_start();
if (isset($_SESSION['username'])) {
    header("Location:../pages/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Facial Recognition Attendance</title>
  <link rel="stylesheet" href="..\styles\login_style.css">

</head>
<body>
  <form class="login-container" method="post" action="../classes/login_check.php">
    <h1>Welcome</h1>
    <h4>input your username and password</h4>
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
    <p><br> or<br></p>
    <p type="text"> <a href="signup.php">Signup</a></p>
  </form>
</body>
</html>
