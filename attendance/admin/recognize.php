<?php define("MODE", "recognize"); ?>
<!DOCTYPE html>
<html>
<head>
  <link rel="icon" href="data:,">
  <title>Recognize Attendance</title>
  <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
  <link rel="stylesheet" href="..\styles\attendancerecognize_style.css">
 
</head>
<body>
  <br>
  <a href="..\admin\index-admin.php" class="back-btn">🔙 Dashboard</a>
  <h1>Camera Recognize Test </h1>
  <button id="startCam">Start Camera</button><br>

  <div class="video-container">
    <video id="video" width="400" height="300" autoplay muted></video>
    <canvas id="overlay" width="400" height="300"></canvas>
    
  </div>
  <br>
 <p id="status">Idle...</p>
  
  <script>const MODE = "<?php echo MODE; ?>";</script>
  <script defer src="../assets/camera_test.js"></script>
</body>
</html>
