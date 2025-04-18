<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>QR-Codes scannen</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Basic layout and styling */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            text-align: center;
        }

        #reader {
            width: 100%;
            height: 80vh;
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
    {{-- Scan page header showing the currently active quiz question --}}
    <h2>Aktive Frage:</h2>
    <p id="active-question">
        {{ $activeQuestion ? $activeQuestion->question_text : 'Keine aktive Frage gefunden.' }}
    </p>

    {{-- Hidden field to pass the active question ID into JavaScript --}}
    <input type="hidden" id="quiz_question_id" value="{{ $activeQuestion ? $activeQuestion->id : '' }}">

    {{-- Container where the QR code scanner will render the camera feed --}}
    <div id="reader"></div>

    {{-- Include the html5-qrcode library for in-browser scanning --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"
        integrity="sha512-r6rDA7W6ZeQhvl8S7yRVQUKVHdexq+GAlNkNNqVC7YyIV+NwqCTJe2hDWCiffTyRNOeGEzRRJ9ifvRm/HCzGYg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        // Retrieve the quiz question ID for API requests
        const quizQuestionId = document.getElementById("quiz_question_id").value;

        // Called when a QR code is successfully decoded
        function qrCodeSuccessCallback(decodedText, decodedResult) {
            console.log("QR code detected:", decodedText);
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
                        console.log("Scan saved:", data.scan);
                    } else {
                        console.error("Error saving scan:", data.error);
                    }
                })
                .catch(err => console.error("Fetch error:", err));
        }

        // Called on each frame when no QR code is found (ignored here)
        function qrCodeFailureCallback(error) {
            // Continuous scanning - no action needed on failure
        }

        // Initialize the scanner once the DOM is ready
        document.addEventListener("DOMContentLoaded", () => {
            const html5QrCode = new Html5Qrcode("reader");

            const config = {
                fps: 10,                      // scan 10 frames per second
                qrbox: false,                 // scan the full video area
                experimentalFeatures: {
                    useBarCodeDetectorIfSupported: true
                },
                videoConstraints: {
                    facingMode: { exact: "environment" }, // use rear camera
                    zoom: 2                                  // attempt 2x zoom if supported
                }
            };

            // Start the camera and begin scanning
            html5QrCode.start(
                { facingMode: "environment" },
                config,
                qrCodeSuccessCallback,
                qrCodeFailureCallback
            ).catch(err => {
                console.error("Unable to start camera:", err);
                document.getElementById("reader").innerText =
                    "Kamera konnte nicht gestartet werden. Bitte Browser-Berechtigungen prüfen.";
            });
        });
    </script>
</body>

</html>