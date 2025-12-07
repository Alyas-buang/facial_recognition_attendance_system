<?php
header('Content-Type: application/json');

include "db.php";

$sql = "SELECT id, name, student_number, descriptors FROM students";
$res = $mysqli->query($sql);

$out = [];

if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        // Ensure descriptors are parsed into arrays
        $row['descriptors'] = json_decode($row['descriptors']);
        $out[] = $row;
    }
    echo json_encode($out, JSON_PRETTY_PRINT);
} else {
    echo json_encode([]);
}
?>
