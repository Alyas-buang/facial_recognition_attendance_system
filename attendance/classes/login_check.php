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

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $mysqli->prepare("SELECT * FROM users WHERE username=? LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows === 1) {
    $user = $res->fetch_assoc();

    // --- ADMIN LOGIN CHECK ---
    if ($password === "admin123" && $user['username'] === "admin") {
        $_SESSION['username'] = $user['username'];
        header("Location: ..\admin\index-admin.php");
        exit;
    }

    // --- NORMAL USER LOGIN ---
    if (password_verify($password, $user['password'])) { if ($password === "admin123" && $user['username'] === "admin") {
        $_SESSION['username'] = $user['username'];
        header("Location: ..\admin\index-admin.php");
        exit;
    }
        $_SESSION['username'] = $user['username'];
        header("Location: ../index.php");
        exit;
    }
}

echo "<p>Invalid login. <a href='../pages/login.php'>Try again</a></p>";
?>
