<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>QR‑Codes scannen</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            text-align: center;
            background: #f5f5f5;
        }

        #reader {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            border: none;
            box-sizing: border-box;
        }

        #active-question {
            padding: 1rem;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        #status-message {
            margin-top: 1rem;
            padding: 0.5rem;
            border-radius: 4px;
        }

        .success {
            background-color: #dff0d8;
            color: #3c763d;
        }

        .error {
            background-color: #f2dede;
            color: #a94442;
        }

        .camera-controls {
            margin: 1rem 0;
        }

        #start-button,
        #stop-button {
            padding: 0.5rem 1rem;
            margin: 0 0.5rem;
            border: none;
            border-radius: 4px;
            background: #007bff;
            color: white;
            cursor: pointer;
        }

        #stop-button {
            background: #dc3545;
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
    </div>

    <div id="reader"></div>
    <div id="status-message"></div>

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

            let html5QrCode = null;
            let scanning = false;

            // Statusnachrichten anzeigen
            function showStatus(message, isError = false) {
                statusMessageElement.textContent = message;
                statusMessageElement.className = isError ? "error" : "success";
                setTimeout(() => {
                    statusMessageElement.textContent = "";
                    statusMessageElement.className = "";
                }, 3000);
            }

            function qrCodeSuccessCallback(decodedText, decodedResult) {
                console.log("Erkannt:", decodedText);

                // Vibration als Feedback, falls unterstützt
                if (navigator.vibrate) {
                    navigator.vibrate(200);
                }

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
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showStatus("QR-Code erfolgreich gescannt!", false);
                            console.log("Speicher ok", data.scan);
                        } else {
                            showStatus("Fehler beim Speichern: " + data.error, true);
                            console.warn("Speicher-Error:", data.error);
                        }
                    })
                    .catch(err => {
                        showStatus("Server-Fehler beim Speichern", true);
                        console.error("Fetch-Error:", err);
                    });
            }

            // Init Scanner und Start-Button-Handler
            function initScanner() {
                html5QrCode = new Html5Qrcode(readerElementId);

                startButton.addEventListener("click", startScanner);
                stopButton.addEventListener("click", stopScanner);
            }

            // Scanner starten
            function startScanner() {
                if (scanning) return;

                const config = {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0,
                    formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE]
                };

                Html5Qrcode.getCameras()
                    .then(cameras => {
                        if (!cameras || cameras.length === 0) {
                            showStatus("Keine Kamera gefunden.", true);
                            return;
                        }

                        // Suche bevorzugt Rückkamera ("environment"), fallback auf erstes Gerät
                        let cameraId = cameras[0].id;
                        for (let cam of cameras) {
                            if (/back|rear|environment/i.test(cam.label)) {
                                cameraId = cam.id;
                                break;
                            }
                        }

                        html5QrCode.start(
                            { facingMode: "environment" },
                            config,
                            qrCodeSuccessCallback,
                            (error) => { /* Ignorieren */ }
                        )
                            .then(() => {
                                scanning = true;
                                startButton.style.display = "none";
                                stopButton.style.display = "inline-block";
                            })
                            .catch(err => {
                                showStatus("Kamera konnte nicht gestartet werden: " + err, true);
                                console.error("Kamera-Start-Fehler:", err);
                            });

                    })
                    .catch(err => {
                        showStatus("Kamera-Zugriff nicht möglich.", true);
                        console.error("getCameras-Fehler:", err);
                    });
            }

            // Scanner stoppen
            function stopScanner() {
                if (!scanning) return;

                html5QrCode.stop()
                    .then(() => {
                        scanning = false;
                        startButton.style.display = "inline-block";
                        stopButton.style.display = "none";
                    })
                    .catch(err => {
                        console.error("Stop-Fehler:", err);
                    });
            }

            // Initialisieren
            initScanner();
        });
    </script>
</body>

</html>