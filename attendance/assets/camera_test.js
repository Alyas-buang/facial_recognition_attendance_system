console.log("[capture.js] loaded");

const video = document.getElementById("video");
const statusEl = document.getElementById("status");
const startBtn = document.getElementById("startCam");
const captureBtn = document.getElementById("capture");
const overlay = document.getElementById("overlay");
const overlayCtx = overlay ? overlay.getContext("2d") : null;
const canvas = document.createElement("canvas");

function setStatus(msg) {
  console.log("[status]", msg); 
  if (statusEl) statusEl.innerText = msg;
}

// --------- Load Face API Models ---------
async function loadModels() {
  setStatus("Loading models...");
  const MODEL_URL = "/attendance/models"; // adjust if needed
  await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
  await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
  await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
  setStatus("Models loaded ✔");
}

// --------- Start Camera ---------
async function startCamera() {
  try {
    setStatus("Requesting camera...");
    const devices = await navigator.mediaDevices.enumerateDevices();
    const cameras = devices.filter(d => d.kind === "videoinput");

    // Prefer Iriun cam if available
    const iriunCam = cameras.find(c => c.label.toLowerCase().includes("iriun"));
    const constraints = iriunCam
      ? { video: { deviceId: { exact: iriunCam.deviceId } } }
      : { video: true };

    const stream = await navigator.mediaDevices.getUserMedia(constraints);
    video.srcObject = stream;
    setStatus("Camera started ✔");
  } catch (err) {
    console.error("Camera error:", err);
    setStatus("Camera error: " + err.message);
  }
}




// --------- Recognition Mode ---------
async function startRecognition() {
  await loadModels();
  await startCamera();

  setStatus("Loading students...");
  const res = await fetch("../classes/students.php");
  const students = await res.json();
  console.log("[students]", students);

  const labeledDescriptors = students.map(
    (s) =>
      new faceapi.LabeledFaceDescriptors(
        s.student_number,
        [new Float32Array(s.descriptors)]
      )
  );

  const faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.6);
  setStatus("Face recognition active");

  let lastScan = 0;

  video.addEventListener("play", () => {
    const displaySize = { width: video.width, height: video.height };
    faceapi.matchDimensions(overlay, displaySize);

    setInterval(async () => {
      const detections = await faceapi
        .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
        .withFaceLandmarks()
        .withFaceDescriptors();

      overlayCtx.clearRect(0, 0, overlay.width, overlay.height);

      if (!detections.length) return;

      const resizedDetections = faceapi.resizeResults(detections, displaySize);

      resizedDetections.forEach(async (det) => {
        const best = faceMatcher.findBestMatch(det.descriptor);

        // Draw box + label
        const box = det.detection.box;
        const drawBox = new faceapi.draw.DrawBox(box, {
          label: best.label !== "unknown" ? `ID: ${best.label}` : "Unknown",
        });
        drawBox.draw(overlay);

        if (best.label !== "unknown") {
          const now = Date.now();
          if (now - lastScan < 5000) return; // throttle
          lastScan = now;

          

          
          try {
            const response = await fetch("../classes/camera_test.php", {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: payload,
            });

            const data = await response.json();
            console.log("[mark_student response]", data);
            setStatus(data.message || data.error || "Unknown response");
          } catch (err) {
           
            
          }
        }
      });
    }, 200);
  });
}

// --------- Button binding ---------
if (startBtn) {
  startBtn.addEventListener("click", () => {
    if (typeof MODE !== "undefined" && MODE === "recognize") {
      startRecognition();
    
    } else {
      setStatus("MODE not set!");
    }
  });
}
