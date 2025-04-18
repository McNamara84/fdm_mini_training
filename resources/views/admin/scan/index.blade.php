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
            height: 100vh;
            /* Volle Höhe */
            margin: 0;
            border: none;
            box-sizing: border-box;
        }

        #active-question {
            padding: 1rem;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            font-size: 1.2rem;
        }
    </style>
</head>

<body>

    <div id="active-question">
        <strong>Aktive Frage:</strong><br>
        {{ $activeQuestion ? $activeQuestion->question_text : 'Keine aktive Frage gefunden.' }}
    </div>
    <input type="hidden" id="quiz_question_id" value="{{ $activeQuestion?->id }}">

    <div id="reader"></div>

    <!-- html5-qrcode ohne integrity-Attribut -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/minified/html5-qrcode.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const quizQuestionId = document.getElementById("quiz_question_id").value;
            const readerElementId = "reader";

            function qrCodeSuccessCallback(decodedText, decodedResult) {
                console.log("Erkannt:", decodedText);
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
                        if (data.success) console.log("Speicher ok", data.scan);
                        else console.warn("Speicher-Error:", data.error);
                    })
                    .catch(err => console.error("Fetch-Error:", err));
            }

            function qrCodeFailureCallback(error) {
                // Ignorieren
            }

            // Scanner initialisieren
            const html5QrCode = new Html5Qrcode(readerElementId);

            // Alle Kameras abfragen, Rückkamera auswählen
            Html5Qrcode.getCameras().then(cameras => {
                if (!cameras || cameras.length === 0) {
                    document.getElementById(readerElementId).innerText = "Keine Kamera gefunden.";
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

                const config = {
                    fps: 30,                 // schnelleres Scanning
                    qrbox: false,            // gesamtes Bild scannen
                    experimentalFeatures: {
                        useBarCodeDetectorIfSupported: true
                    },
                    videoConstraints: {
                        deviceId: { exact: cameraId },
                        // Versuche Zoom und Torch, falls unterstützt:
                        advanced: [
                            { zoom: 2.0 },
                            { torch: true }
                        ]
                    }
                };

                html5QrCode.start(
                    cameraId,
                    config,
                    qrCodeSuccessCallback,
                    qrCodeFailureCallback
                ).catch(err => {
                    console.error("Kamera-Start-Fehler:", err);
                    document.getElementById(readerElementId).innerText =
                        "Kamera konnte nicht gestartet werden. Bitte Berechtigungen prüfen.";
                });

            }).catch(err => {
                console.error("getCameras-Fehler:", err);
                document.getElementById(readerElementId).innerText =
                    "Kamera-Übersicht konnte nicht geladen werden.";
            });
        });
    </script>
</body>

</html>