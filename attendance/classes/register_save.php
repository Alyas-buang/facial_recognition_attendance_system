<?php
header('Content-Type: application/json');

include "db.php";

// Parse JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['name']) || empty($data['student_number']) || empty($data['descriptor'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing required fields"]);
    exit;
}

$name = $mysqli->real_escape_string($data['name']);
$student_number = $mysqli->real_escape_string($data['student_number']);
$descriptor = json_encode($data['descriptor']);

// Insert or update student
$sql = "INSERT INTO students (name, student_number, descriptors)
        VALUES ('$name', '$student_number', '$descriptor')
        ON DUPLICATE KEY UPDATE 
            name = VALUES(name),
            descriptors = VALUES(descriptors)";

if ($mysqli->query($sql)) {
    echo json_encode(["message" => "Student registered successfully", "student_number" => $student_number]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "DB error: " . $mysqli->error]);
}
?>
