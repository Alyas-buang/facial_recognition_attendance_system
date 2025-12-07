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

<?php define("MODE", "recognize"); ?>
<!DOCTYPE html>
<html lang="mandarin">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="data:,">
  <title>Attendance & Recognition</title>
  <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
  <link rel="stylesheet" href="..\styles\attendancerecognize_style.css">
  
 
</head>
<body>
  <a href="..\admin\index-admin.php" class="back-btn">🔙 Dashboard</a>

  <h1>👤 Attendance & Recognition</h1>
  <button id="startCam">Start Camera</button><br>

  <div class="video-container">
    <video id="video" width="400" height="300" autoplay muted></video>
    <canvas id="overlay" width="400" height="300"></canvas>
  </div>
<br>
  <p id="status">Idle...</p>

  <!-- Attendance table -->
  <h2>📋 Attendance Logs</h2>
  <table id="attendanceTable">
    <thead>
      <tr>
        <th>Student Number</th>
        <th>Name</th>
        <th>Last Seen</th>
      </tr>
    </thead>
    <tbody>
      <tr><td colspan="3">No records yet</td></tr>
    </tbody>
  </table>

  <script>const MODE = "<?php echo MODE; ?>";</script>
  <script defer src="../assets/capture.js"></script>
  <script>
    // Refresh attendance logs dynamically
    async function loadAttendance() {
      try {
        const res = await fetch("../classes/attendance_api.php");
        if (!res.ok) throw new Error("Failed to load");
        const data = await res.json();
        const tbody = document.querySelector("#attendanceTable tbody");
        tbody.innerHTML = "";
        if (!data.length) {
          tbody.innerHTML = "<tr><td colspan='3'>No attendance records</td></tr>";
        } else {
          data.forEach(row => {
            tbody.innerHTML += `
              <tr>
                <td>${row.student_number}</td>
                <td>${row.name}</td>
                <td>${row.timestamp}</td>
              </tr>`;
          });
        }
      } catch (err) {
        console.error("[attendance fetch error]", err);
      }
    }

    // Auto-refresh every 10 seconds
    setInterval(loadAttendance, 10000);
    loadAttendance();
  </script>
</body>
</html>
