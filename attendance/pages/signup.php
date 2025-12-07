
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Signup | Facial Recognition Attendance</title>
  <link rel="stylesheet" href="..\styles\default_style.css">
</head>
<body>
  <form class="login-container" method="post" action="../classes/signup_check.php">
    <h1>Create Account</h1>
    <p>Enter your username and password</p>

    <input type="text" name="username" placeholder="Username" required>
   
    <input type="text" name="password" placeholder="Password" required>

    <button type="submit">Signup</button>

    <h3><br>or<br></h3>

    <p type="text"> <a href="login.php">Log-in</a></p>
  </form>
</body>
</html>