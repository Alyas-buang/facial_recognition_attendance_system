<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "attendance_db";

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) {
    die("DB connection failed: " . $mysqli->connect_error);
}
?>
