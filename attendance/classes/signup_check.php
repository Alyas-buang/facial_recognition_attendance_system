<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db   = "attendance_db";

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) {
    die("DB connection failed: " . $mysqli->connect_error);
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    echo "<p>Please fill all fields. <a href='../pages/signup.php'>Try again</a></p>";
    exit;
}

// CHECK IF USER ALREADY EXISTS
$check = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
$check->bind_param("s", $username);
$check->execute();
$res = $check->get_result();

if ($res->num_rows > 0) {
    echo "<p>Username already taken. <a href='../pages/signup.php'>Try again</a></p>";
    exit;
}

// HASH THE PASSWORD
$hashed = password_hash($password, PASSWORD_DEFAULT);

// INSERT NEW USER
$stmt = $mysqli->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $hashed);

if ($stmt->execute()) {
    // AUTO LOGIN AFTER SIGNUP
    $_SESSION['username'] = $username;
    header("location:..\pages\login_redirect.php");
    exit;
} else {
    echo "<p>Signup failed. <a href='../pages/signup.php'>Try again</a></p>";
    exit;
}
?>