<?php define("MODE", "register"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="data:,">
  <title>Register Student</title>

  <!-- Face API -->
  <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
  <link rel="stylesheet" href="..\styles\register_style.css">
  

</head>
<body>
  <a href="..\index.php" class="back-btn">🔙 Dashboard</a>
  <h1>📸 Register Student</h1>

  <input id="name" placeholder="Full Name"><br>
  <input id="student_number" placeholder="Student Number"><br>

  <button id="startCam">Start Camera</button>
  <button id="capture">Capture & Save</button><br>

  <div class="video-container">
    <video id="video" width="400" height="300" autoplay muted></video>
    <canvas id="overlay" width="400" height="300"></canvas>
  </div>

  <p id="status">Idle...</p>

  <script>const MODE = "<?php echo MODE; ?>";</script>
  <script defer src="../assets/capture.js"></script>
</body>
</html>