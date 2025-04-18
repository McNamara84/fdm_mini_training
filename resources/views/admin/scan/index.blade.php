<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>QR‑Codes scannen</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <!-- Verhindert Zoom auf mobilen Geräten -->
    <meta http-equiv="permissions-policy" content="camera=(), microphone=()">
    <!-- Explizite Kamera-Richtlinie -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            text-align: center;
            background: #f5f5f5;
            overscroll-behavior: none;
            /* Verhindert Pull-to-Refresh */
        }

        #reader {
            width: 100%;
            max-width: 95vw;
            /* Anpassung für mobile Geräte */
            height: 50vh;
            margin: 0 auto;
            border: 2px solid #ddd;
            box-sizing: border-box;
            overflow: hidden;
            /* Verhindert Scrolling innerhalb des Readers */
        }

        #active-question {
            padding: 1rem;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        #status-message {
            margin: 1rem auto;
            padding: 0.8rem;
            border-radius: 4px;
            max-width: 90%;
            word-break: break-word;
        }

        .success {
            background-color: #dff0d8;
            color: #3c763d;
        }

        .error {
            background-color: #f2dede;
            color: #a94442;
        }

        .info {
            background-color: #d9edf7;
            color: #31708f;
        }

        .camera-controls {
            margin: 1rem 0;
        }

        #start-button,
        #stop-button,
        #switch-camera-button {
            padding: 0.8rem 1.2rem;
            margin: 0.5rem;
            border: none;
            border-radius: 4px;
            background: #007bff;
            color: white;
            cursor: pointer;
            font-size: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        #stop-button {
            background: #dc3545;
        }

        #switch-camera-button {
            background: #6c757d;
        }

        .debug-info {
            font-size: 0.8rem;
            color: #666;
            margin-top: 1rem;
            padding: 0.5rem;
            border-top: 1px solid #ddd;
        }
    </style>
</head>

<body>

    <div id="active-question">
        <strong>Aktive Frage:</strong><br>
        {{ $activeQuestion ? $activeQuestion->question_text : 'Keine aktive Frage gefunden.' }}
    </div>
    <input type="hidden" id="quiz_question_id" value="{{ $activeQuestion?->id }}">

    <div class="camera-controls">
        <button id="start-button">Scanner starten</button>
        <button id="stop-button" style="display: none;">Scanner stoppen</button>
        <button id="switch-camera-button" style="display: none;">Kamera wechseln</button>
    </div>

    <div id="reader"></div>
    <div id="status-message" class="info">Klicke auf "Scanner starten", um QR-Codes zu scannen.</div>
    <div id="debug-info" class="debug-info"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"
        integrity="sha512-r6rDA7W6ZeQhvl8S7yRVQUKVHdexq+GAlNkNNqVC7YyIV+NwqCTJe2hDWCiffTyRNOeGEzRRJ9ifvRm/HCzGYg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const quizQuestionId = document.getElementById("quiz_question_id").value;
            const readerElementId = "reader";
            const statusMessageElement = document.getElementById("status-message");
            const startButton = document.getElementById("start-button");
            const stopButton = document.getElementById("stop-button");
            const switchCameraButton = document.getElementById("switch-camera-button");
            const debugInfoElement = document.getElementById("debug-info");

            let html5QrCode = null;
            let scanning = false;
            let currentCameraId = null;
            let availableCameras = [];
            let cameraIndex = 0;

            // Debug-Informationen anzeigen
            function showDebugInfo(message) {
                debugInfoElement.innerHTML += message + "<br>";
                console.log(message);
            }

            // Protokolliere die Browser-/Geräteinformationen
            showDebugInfo("User-Agent: " + navigator.userAgent);
            showDebugInfo("Protokoll: " + window.location.protocol);

            // Prüfe HTTPS
            if (window.location.protocol !== 'https:' && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
                showStatus("Warnung: Kamerazugriff erfordert HTTPS für die meisten Browser!", true);
            }

            // Statusnachrichten anzeigen
            function showStatus(message, isError = false, isInfo = false) {
                statusMessageElement.textContent = message;
                statusMessageElement.className = isError ? "error" : (isInfo ? "info" : "success");
                showDebugInfo(message);
            }

            function qrCodeSuccessCallback(decodedText, decodedResult) {
                // Vibration als Feedback, falls unterstützt
                if (navigator.vibrate) {
                    navigator.vibrate(200);
                }

                // Audio-Feedback
                let audio = new Audio('data:audio/mp3;base64,SUQzAwAAAAAfdlRJVDIAAAAZAAAASFRNTDUgQXVkaW8gQmVlcCBTb3VuZABUWUVSAAAABgAAADIwMjAAAABUQ09OAAAABgAAAEJlZXAAAABUQUxCAAAABgAAAEJlZXAAAABUUkNLAAAAAgAAADEAVFBFMQAAABcAAABRUiBDb2RlIFNjYW5uZWQgQmVlcAAAA//uQxAADxOIC9CMAu5EQQF0EQZduCPAAXA9DwPROD8nF9j//f+9D0YYXcf79OD0Y4Pg+D7thmEd3Z//b/OE2bY4zh+D4Pv8MPL6GHMXZ0f/////c6Hyc3HT//9ysr/6HMXOdD46HV//0PYZcN+4PL9Dz3//+hyn0E1AQQEAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=');
                audio.play();

                // QR-Code-Daten anzeigen
                showStatus("QR-Code erkannt: " + decodedText);

                // Speichern des Scans
                fetch("{{ route('admin.scan.store') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        qr_data: decodedText,
                        quiz_question_id: quizQuestionId
                    })
                })
                    .then(res => {
                        if (!res.ok) {
                            throw new Error(`HTTP-Status: ${res.status}`);
                        }
                        return res.json();
                    })
                    .then(data => {
                        if (data.success) {
                            showStatus("QR-Code erfolgreich gespeichert!", false);
                        } else {
                            showStatus("Fehler beim Speichern: " + (data.error || "Unbekannter Fehler"), true);
                        }
                    })
                    .catch(err => {
                        showStatus("Netzwerkfehler: " + err.message, true);
                    });
            }

            // Init Scanner
            function initScanner() {
                try {
                    html5QrCode = new Html5Qrcode(readerElementId);
                    showDebugInfo("Scanner initialisiert");

                    startButton.addEventListener("click", startScanner);
                    stopButton.addEventListener("click", stopScanner);
                    switchCameraButton.addEventListener("click", switchCamera);
                } catch (error) {
                    showStatus("Fehler bei Scanner-Initialisierung: " + error.message, true);
                }
            }

            // Kamera wechseln
            function switchCamera() {
                if (scanning && availableCameras.length > 1) {
                    stopScanner().then(() => {
                        cameraIndex = (cameraIndex + 1) % availableCameras.length;
                        currentCameraId = availableCameras[cameraIndex].id;
                        startScanner();
                    });
                }
            }

            // Scanner starten
            function startScanner() {
                if (scanning) return;

                showStatus("Scanner wird gestartet...", false, true);

                const qrboxFunction = function (viewfinderWidth, viewfinderHeight) {
                    let minEdgePercentage = 0.7; // Use 70% of the smaller dimension
                    let minEdgeSize = Math.min(viewfinderWidth, viewfinderHeight);
                    let qrboxSize = Math.floor(minEdgeSize * minEdgePercentage);
                    return {
                        width: qrboxSize,
                        height: qrboxSize
                    };
                };

                const config = {
                    fps: 10,
                    qrbox: qrboxFunction,
                    aspectRatio: window.innerWidth / window.innerHeight,
                    formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE],
                    showTorchButtonIfSupported: true,
                    showZoomSliderIfSupported: true
                };

                try {
                    // Kamerazugriff anfordern
                    Html5Qrcode.getCameras()
                        .then(cameras => {
                            availableCameras = cameras;

                            if (!cameras || cameras.length === 0) {
                                showStatus("Keine Kamera gefunden. Bitte erlaube den Kamerazugriff und versuche es erneut.", true);
                                return;
                            }

                            showDebugInfo(`${cameras.length} Kamera(s) gefunden`);
                            cameras.forEach((camera, idx) => {
                                showDebugInfo(`Kamera ${idx}: ${camera.id} (${camera.label})`);
                            });

                            // Zeige Kamera-Wechsel-Knopf, wenn mehrere Kameras vorhanden sind
                            if (cameras.length > 1) {
                                switchCameraButton.style.display = "inline-block";
                            }

                            // Wenn noch keine Kamera ausgewählt wurde, wähle eine
                            if (!currentCameraId) {
                                // Suche bevorzugt Rückkamera ("environment"), fallback auf erstes Gerät
                                currentCameraId = cameras[0].id;
                                for (let i = 0; i < cameras.length; i++) {
                                    if (/back|rear|environment/i.test(cameras[i].label)) {
                                        currentCameraId = cameras[i].id;
                                        cameraIndex = i;
                                        break;
                                    }
                                }
                            }

                            showDebugInfo(`Starte Kamera: ${currentCameraId}`);

                            // Versuche zuerst mit deviceId
                            html5QrCode.start(
                                { deviceId: { exact: currentCameraId } },
                                config,
                                qrCodeSuccessCallback,
                                () => { } // Ignoriere Fehler beim Scannen
                            )
                                .then(() => {
                                    scanning = true;
                                    startButton.style.display = "none";
                                    stopButton.style.display = "inline-block";
                                    showStatus("Scanner aktiv. Halte einen QR-Code in die Kamera.", false, true);
                                })
                                .catch(err => {
                                    // Wenn es mit deviceId nicht klappt, versuche mit facingMode
                                    showDebugInfo(`Fehler mit deviceId: ${err}, versuche facingMode`);

                                    html5QrCode.start(
                                        { facingMode: "environment" },
                                        config,
                                        qrCodeSuccessCallback,
                                        () => { }
                                    )
                                        .then(() => {
                                            scanning = true;
                                            startButton.style.display = "none";
                                            stopButton.style.display = "inline-block";
                                            showStatus("Scanner aktiv. Halte einen QR-Code in die Kamera.", false, true);
                                        })
                                        .catch(facingModeErr => {
                                            showStatus(`Kamera konnte nicht gestartet werden: ${facingModeErr.message}`, true);
                                        });
                                });
                        })
                        .catch(err => {
                            showStatus(`Kamerazugriff verweigert oder nicht möglich: ${err.message}`, true);
                        });
                } catch (error) {
                    showStatus(`Unerwarteter Fehler: ${error.message}`, true);
                }
            }

            // Scanner stoppen
            function stopScanner() {
                if (!scanning) return Promise.resolve();

                return html5QrCode.stop()
                    .then(() => {
                        scanning = false;
                        startButton.style.display = "inline-block";
                        stopButton.style.display = "none";
                        showStatus("Scanner gestoppt.", false, true);
                    })
                    .catch(err => {
                        showStatus(`Fehler beim Stoppen: ${err.message}`, true);
                    });
            }

            // Initialisieren
            initScanner();
        });
    </script>
</body>

</html>