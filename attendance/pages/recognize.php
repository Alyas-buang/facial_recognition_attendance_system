<?php define("MODE", "recognize"); ?>
<!DOCTYPE html>
<html>
<head>
  <link rel="icon" href="data:,">
  <title>Recognize Attendance</title>
  <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
  <link rel="stylesheet" href="..\styles\recognize_style.css">
 
</head>
<body>
  <h1>Recognize Attendance</h1>
  <button id="startCam">Start Camera</button><br>

  <div class="video-container">
    <video id="video" width="400" height="300" autoplay muted></video>
    <canvas id="overlay" width="400" height="300"></canvas>
  </div>

  <p id="status">Idle...</p>
  <p><a href="attendance.php">📋 View Attendance Logs</a></p>

  <script>const MODE = "<?php echo MODE; ?>";</script>
  <script defer src="../assets/capture.js"></script>
</body>
</html>
