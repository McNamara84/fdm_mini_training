<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>QR-Codes scannen</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        #reader {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <!-- Anzeige der aktuell aktiven Frage als Hinweis für den Trainingsleiter -->
    <h2>Aktive Frage:</h2>
    <p id="active-question">
        {{ $activeQuestion ? $activeQuestion->question_text : 'Keine aktive Frage gefunden.' }}
    </p>

    <!-- Hidden-Input mit der aktuell aktiven Frage-ID -->
    <input type="hidden" id="quiz_question_id" value="{{ $activeQuestion ? $activeQuestion->id : '' }}">

    <div id="reader"></div>

    <!-- Einbindung der html5-qrcode Bibliothek vom CDN -->
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        // Callback-Funktion, die aufgerufen wird, wenn ein QR-Code erfolgreich gelesen wurde.
        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
            console.log("QR-Code erkannt:", decodedText);

            // Verwende den Wert des versteckten Inputs als aktuelle Frage-ID
            const quizQuestionId = document.getElementById("quiz_question_id").value;

            // Sende einen AJAX-Request an den Controller, um den Scan zu speichern.
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
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log("Scan gespeichert:", data.scan);
                } else {
                    console.error("Fehler beim Speichern:", data.error);
                }
            })
            .catch(error => console.error("Fehler bei der Anfrage:", error));
        };

        // Konfiguration des QR-Code Scanners
        const html5QrcodeScanner = new Html5QrcodeScanner(
            "reader",
            { fps: 10, qrbox: 250 },
            false  // verbose = false
        );
        html5QrcodeScanner.render(qrCodeSuccessCallback);
    </script>
</body>
</html>
