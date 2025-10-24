<?php
include "db.php";

// Select latest attendance per student
$sql = "
  SELECT s.id, s.name, s.student_number, MAX(a.timestamp) AS last_seen
  FROM students s
  LEFT JOIN attendance a ON a.student_id = s.id
  GROUP BY s.id, s.name, s.student_number
  ORDER BY last_seen DESC
";
$res = $mysqli->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Attendance Logs</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
    th { background: #f2f2f2; }
  </style>
</head>
<body>
  <h1>Attendance Logs</h1>
  <table>
    <tr>
      <th>ID</th>
      <th>Student Name</th>
      <th>Student Number</th>
      <th>Last Seen</th>
    </tr>
    <?php if ($res && $res->num_rows > 0): ?>
      <?php while ($row = $res->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['student_number']) ?></td>
          <td><?= $row['last_seen'] ?: "Never" ?></td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="4">No students found</td></tr>
    <?php endif; ?>
  </table>
</body>
</html>
