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

if ($check && $check->num_rows > 0) {
    // Update existing record
    $att = $check->fetch_assoc();
    $attendance_id = $att['id'];
   
    echo json_encode([
        "message" => "Camera active",
        
        
    ]);
} 