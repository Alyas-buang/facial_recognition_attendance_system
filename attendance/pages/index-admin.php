<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: pages\login.php");
    exit;
}
?>
<DOCTYPE html>
<html lang="en">
<head>
  <met!a charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | Facial Recognition Attendance</title>
  <link rel="stylesheet" href="styles\index_style.css">
  
</head>
<body>
  <div class="dashboard">
    <h1>Welcome, <?= htmlspecialchars($_SESSION['username']); ?> 👋</h1>
    <p>Select an option below</p>

    <a href="pages\register.php">📷 Register Student</a>
    <a href="pages\attendance_recognize.php">👤 Recognize Attendance</a>
    <a href="pages\attendance.php">📋 Attendance Logs</a>

    <a href="classes\logout.php" class="logout">🚪 Logout</a>
  </div>
</body>
</html>
