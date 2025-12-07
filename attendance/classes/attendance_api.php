<?php
include "../classes/db.php";

header("Content-Type: application/json");

$sql = "SELECT a.id, s.name, s.student_number, a.timestamp
        FROM attendance a
        JOIN students s ON a.student_id = s.id
        ORDER BY a.timestamp DESC";

$res = $mysqli->query($sql);
$out = [];

if ($res) {
    while ($row = $res->fetch_assoc()) {
        $out[] = $row;
    }
}

echo json_encode($out);
