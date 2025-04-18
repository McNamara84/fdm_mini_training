<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>QR‑Codes scannen</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans‑serif;
            background: #f0f0f0;
            text-align: center;
        }

        #active-question {
            background: #fff;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            font-size: 1.2rem;
        }

        #reader {
            width: 100%;
            height: calc(100vh - 4rem);
            box-sizing: border-box;
        }
    </style>
</head>

<body>

    <div id="active-question">
        <strong>Aktive Frage:</strong><br>
        {{ $activeQuestion?->question_text ?: 'Keine aktive Frage gefunden.' }}
    </div>
    <input type="hidden" id="quiz_question_id" value="{{ $activeQuestion?->id }}">

    <div id="reader"></div>

    <!-- Einfache Einbindung ohne integrity-Attribut -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/minified/html5-qrcode.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const questionId = document.getElementById("quiz_question_id").value;

            function onScanSuccess(text) {
                console.log("Erkannt:", text);
                fetch("{{ route('admin.scan.store') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        qr_data: text,
                        quiz_question_id: questionId
                    })
                }).then(r => r.json())
                    .then(j => console.log(j.success ? "Gespeichert" : "Fehler", j))
                    .catch(e => console.error(e));
            }

            function onScanFailure(error) {
                // kein Logging, um Konsole nicht zu überfluten
            }

            const html5QrCode = new Html5Qrcode("reader");

            // 1. Versuche Kamera-Liste, um gezielt Rückkamera zu wählen
            Html5Qrcode.getCameras().then(cameras => {
                const back = cameras.find(cam => /back|rear|environment/i.test(cam.label));
                const cameraId = back ? back.id : cameras[0].id;
                console.log("Verwende Kamera:", back ? back.label : cameras[0].label);

                // 2. Starte den Scanner mit einfachem Config
                html5QrCode.start(
                    { deviceId: { exact: cameraId } },
                    { fps: 15, qrbox: false },
                    onScanSuccess,
                    onScanFailure
                ).catch(err => {
                    console.error("Start-Fehler:", err);
                    // Fallback: nur facingMode
                    html5QrCode.start(
                        { facingMode: "environment" },
                        { fps: 10, qrbox: false },
                        onScanSuccess,
                        onScanFailure
                    ).catch(e => {
                        console.error("Fallback-Fehler:", e);
                        document.getElementById("reader").innerText =
                            "Kamera konnte nicht gestartet werden. Bitte Berechtigungen prüfen.";
                    });
                });

            }).catch(err => {
                console.error("getCameras-Fehler:", err);
                // Direkt mit facingMode starten
                html5QrCode.start(
                    { facingMode: "environment" },
                    { fps: 10, qrbox: false },
                    onScanSuccess,
                    onScanFailure
                ).catch(e => {
                    console.error("Fallback-Fehler:", e);
                    document.getElementById("reader").innerText =
                        "Kamera konnte nicht gestartet werden. Bitte Berechtigungen prüfen.";
                });
            });
        });
    </script>
</body>

</html>