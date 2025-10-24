<?php define("MODE", "register"); ?>
<!DOCTYPE html>
<html>
<head>
  <link rel="icon" href="data:,">
  <title>Register Student</title>
  <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
  <style>
    body { font-family: Arial, sans-serif; text-align: center; margin-top: 20px; }
    input, button { margin: 5px; padding: 6px; font-size: 14px; }
    .video-container {
      position: relative;
      display: inline-block;
      margin-top: 10px;
    }
    video, canvas {
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    canvas {
      position: absolute;
      top: 0;
      left: 0;
    }
    #status { margin-top: 10px; font-weight: bold; }
  </style>
</head>
<body>
  <h1>Register Student</h1>

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
  <script defer src="capture.js"></script>
</body>
</html>

