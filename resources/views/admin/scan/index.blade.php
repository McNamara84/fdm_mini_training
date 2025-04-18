<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>QR-Codes scannen</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            text-align: center;
        }

        #reader {
            width: 100%;
            height: 80vh;
            /* volles Browser-Viewport nutzen */
            margin: 0 auto;
            border: 2px solid #ccc;
            box-sizing: border-box;
        }

        #active-question {
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <h2>Aktive Frage:</h2>
    <p id="active-question">
        {{ $activeQuestion ? $activeQuestion->question_text : 'Keine aktive Frage gefunden.' }}
    </p>
    <input type="hidden" id="quiz_question_id" value="{{ $activeQuestion ? $activeQuestion->id : '' }}">

    <div id="reader"></div>

    <!-- html5-qrcode Bibliothek -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"
        integrity="sha512-r6rDA7W6ZeQhvl8S7yRVQUKVHdexq+GAlNkNNqVC7YyIV+NwqCTJe2hDWCiffTyRNOeGEzRRJ9ifvRm/HCzGYg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        const quizQuestionId = document.getElementById("quiz_question_id").value;

        // Erfolgs-Callback
        function qrCodeSuccessCallback(decodedText, decodedResult) {
            console.log("QR-Code erkannt:", decodedText);
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
                        console.log("Scan gespeichert:", data.scan);
                    } else {
                        console.error("Fehler beim Speichern:", data.error);
                    }
                })
                .catch(err => console.error("Fetch-Error:", err));
        }

        // Fehler-Callback (wird bei jedem Frame ohne QR-Code aufgerufen)
        function qrCodeFailureCallback(error) {
            // Ignoriere – wir scannen kontinuierlich
        }

        // Wenn document ready
        document.addEventListener("DOMContentLoaded", () => {
            const html5QrCode = new Html5Qrcode("reader");

            const config = {
                fps: 10,
                qrbox: false,  // false = ganze Kamera-Fläche wird gescant
                experimentalFeatures: {
                    useBarCodeDetectorIfSupported: true
                },
                videoConstraints: {
                    facingMode: { exact: "environment" },
                    zoom: 2          // versuche Zoom-Level 2x (falls Kamera es unterstützt)
                }
            };

            html5QrCode.start(
                { facingMode: "environment" },
                config,
                qrCodeSuccessCallback,
                qrCodeFailureCallback
            ).catch(err => {
                console.error("Kamera konnte nicht gestartet werden:", err);
                document.getElementById("reader").innerText =
                    "Kamera konnte nicht gestartet werden. Bitte Browser-Berechtigungen prüfen.";
            });
        });
    </script>
</body>

</html>