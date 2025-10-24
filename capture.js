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

// --------- Registration Mode ---------
async function setupRegistration() {
  await loadModels();
  await startCamera();

  // Live overlay drawing
  video.addEventListener("play", () => {
    const displaySize = { width: video.width, height: video.height, color: green};
    faceapi.matchDimensions(overlay, displaySize);

    setInterval(async () => {
      const detections = await faceapi
        .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
        .withFaceLandmarks();

      overlayCtx.clearRect(0, 0, overlay.width, overlay.height);

      if (detections.length) {
        const resized = faceapi.resizeResults(detections, displaySize);
        resized.forEach(det => {
          const drawBox = new faceapi.draw.DrawBox(det.detection.box, { label: "Face" });
          drawBox.draw(overlay);
        });
      }
    }, 200);
  });

  if (!captureBtn) return;

  captureBtn.addEventListener("click", async () => {
    setStatus("Capturing...");

    // draw current frame to canvas
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext("2d").drawImage(video, 0, 0);

    const img = await faceapi.fetchImage(canvas.toDataURL("image/jpeg"));
    const detection = await faceapi
      .detectSingleFace(img, new faceapi.TinyFaceDetectorOptions())
      .withFaceLandmarks()
      .withFaceDescriptor();

    if (!detection) {
      setStatus("No face detected. Try again.");
      return;
    }

    const descriptor = Array.from(detection.descriptor);
    const name = document.getElementById("name").value.trim();
    const student_number = document.getElementById("student_number").value.trim();

    if (!name || !student_number) {
      alert("Enter name and student number first!");
      return;
    }

    const payload = { name, student_number, descriptor };
    console.log("[register payload]", payload);

    try {
      const res = await fetch("register_save.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });
      const data = await res.json();
      console.log("[register_save response]", data);
      setStatus(data.message || data.error || "Unknown response");
    } catch (err) {
      console.error("[register error]", err);
      setStatus("Failed to save registration");
    }
  });
}

// --------- Recognition Mode ---------
async function startRecognition() {
  await loadModels();
  await startCamera();

  setStatus("Loading students...");
  const res = await fetch("students.php");
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
  setStatus("Ready ✔");

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

          const student_number = best.label;
          const payload = JSON.stringify({ student_number });

          setStatus(`Recognized: ${student_number}`);
          console.log("[sending payload]", payload);

          try {
            const response = await fetch("mark_student.php", {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: payload,
            });

            const data = await response.json();
            console.log("[mark_student response]", data);
            setStatus(data.message || data.error || "Unknown response");
          } catch (err) {
            console.error("[fetch error]", err);
            setStatus("Failed to send attendance");
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
    } else if (typeof MODE !== "undefined" && MODE === "register") {
      setupRegistration();
    } else {
      setStatus("MODE not set!");
    }
  });
}
