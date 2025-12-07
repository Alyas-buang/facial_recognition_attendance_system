<?php
include "../classes/db.php";

// Handle delete request
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $mysqli->query("DELETE FROM attendance WHERE student_id = $id");
  $mysqli->query("DELETE FROM students WHERE id = $id");
  header("Location:pages/attendance.php");
  exit;
}

// Handle update request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'])) {
  $id = intval($_POST['update_id']);
  $name = $mysqli->real_escape_string($_POST['name']);
  $student_number = $mysqli->real_escape_string($_POST['student_number']);
  $mysqli->query("UPDATE students SET name='$name', student_number='$student_number' WHERE id=$id");
  header("Location:pages/attendance.php");
  exit;
}

// Fetch attendance
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
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Attendance Logs</title>
  <link rel="icon" href="data:,">
  <link rel="stylesheet" href="..\styles\attendancelogs_style.css">
  
</head>
<body>
  <div class="top-bar">
    <a href="..\admin\index-admin.php" class="back-btn">🔙 Dashboard</a>
    <select id="filterSelect" onchange="filterRows()">
      <option value="all">📋 Show All</option>
      <option value="green">🟢 Present (within 1 hr)</option>
      <option value="yellow">🟡 Seen Today</option>
      <option value="red">🔴 Absent</option>
    </select>
  </div>

  <h1>📋 Attendance Logs</h1>

  <table id="attendanceTable">
    <tr>
      <th>Status</th>
      <th>Student Name</th>
      <th>Student Number</th>
      <th>Last Seen</th>
      <th>Actions</th>
    </tr>

    <?php
    $now = new DateTime();

    if ($res && $res->num_rows > 0):
      while ($row = $res->fetch_assoc()):
        $statusClass = "red"; // default absent
        $lastSeen = $row['last_seen'];

        if ($lastSeen) {
          $lastSeenTime = new DateTime($lastSeen);
          $diff = $now->getTimestamp() - $lastSeenTime->getTimestamp();

          if ($diff <= 3600) $statusClass = "green"; // within 1 hour
          elseif ($lastSeenTime->format('Y-m-d') === $now->format('Y-m-d')) $statusClass = "yellow"; // seen today
        }
    ?>
      <tr data-status="<?= $statusClass ?>">
        <td><span class="status-dot <?= $statusClass ?>"></span></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['student_number']) ?></td>
        <td><?= $row['last_seen'] ?: "Never" ?></td>
        <td>
          <button class="btn btn-update" onclick="editStudent(<?= $row['id'] ?>, '<?= htmlspecialchars($row['name']) ?>', '<?= htmlspecialchars($row['student_number']) ?>')">✏️ Update</button>
          <button class="btn btn-delete" onclick="deleteStudent(<?= $row['id'] ?>)">🗑️ Delete</button>
        </td>
      </tr>
    <?php endwhile; else: ?>
      <tr><td colspan="6">No students found</td></tr>
    <?php endif; ?>
  </table>

  <script>
    function filterRows() {
      const filter = document.getElementById("filterSelect").value;
      const rows = document.querySelectorAll("#attendanceTable tr[data-status]");

      rows.forEach(row => {
        if (filter === "all" || row.getAttribute("data-status") === filter) {
          row.style.display = "";
        } else {
          row.style.display = "none";
        }
      });
    }

    function editStudent(id, name, number) {
      const newName = prompt("Edit name:", name);
      if (!newName) return;
      const newNumber = prompt("Edit student number:", number);
      if (!newNumber) return;

      const form = document.createElement("form");
      form.method = "POST";
      form.action = "";
      form.innerHTML = `
        <input type="hidden" name="update_id" value="${id}">
        <input type="hidden" name="name" value="${newName}">
        <input type="hidden" name="student_number" value="${newNumber}">
      `;
      document.body.appendChild(form);
      form.submit();
    }

    function deleteStudent(id) {
      if (confirm("Are you sure you want to delete this student and their attendance records?")) {
        window.location.href = "?delete=" + id;
      }
    }
  </script>
</body>
</html>