<?php
header('Content-Type: application/json');
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['student_number'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing student_number"]);
    exit;
}

$student_number = $mysqli->real_escape_string($data['student_number']);

// 1. Find student
$res = $mysqli->query("SELECT id FROM students WHERE student_number='$student_number' LIMIT 1");
if (!$res || $res->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["error" => "Student not found"]);
    exit;
}

$row = $res->fetch_assoc();
$student_id = $row['id'];

// 2. Check if attendance already exists today
$today = date("Y-m-d");
$check = $mysqli->query("SELECT id FROM attendance WHERE student_id=$student_id AND DATE(timestamp)='$today' LIMIT 1");

if ($check && $check->num_rows > 0) {
    // Update existing record
    $att = $check->fetch_assoc();
    $attendance_id = $att['id'];
    $mysqli->query("UPDATE attendance SET timestamp=NOW() WHERE id=$attendance_id");
    echo json_encode([
        "message" => "Attendance updated",
        "student_number" => $student_number,
        "timestamp" => date("Y-m-d H:i:s")
    ]);
} else {
    // Insert new record
    $mysqli->query("INSERT INTO attendance (student_id) VALUES ($student_id)");
    echo json_encode([
        "message" => "Attendance marked",
        "student_number" => $student_number,
        "timestamp" => date("Y-m-d H:i:s")
    ]);
}
