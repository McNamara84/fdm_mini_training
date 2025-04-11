<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>QR-Code Scan – Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        /* Optionale Anpassungen für mobile Geräte */
        #reader {
            max-width: 100%;
            margin: 0 auto;
        }
    </style>
</head>
<body class="bg-gray-100 p-4">
    <div class="container mx-auto">
        <h1 class="text-2xl font-bold mb-4 text-center">QR-Code Scan (Admin)</h1>
        <p class="mb-4 text-center">Der Scan-Vorgang startet automatisch!</p>
        <div id="reader" class="mx-auto"></div>
        <div id="message" class="mt-4 text-center"></div>
    </div>
    
    <!-- html5-qrcode Bibliothek einbinden -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script type="text/javascript">
        // Erstelle eine neue Instanz des Scanners.
        const html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", 
            { fps: 10, qrbox: 250 },
            /* verbose= */ false
        );

        // Bei erfolgreichem Scan wird die Funktion onScanSuccess aufgerufen.
        function onScanSuccess(decodedText, decodedResult) {
            console.log(`QR-Code gescannt: ${decodedText}`);
            document.getElementById('message').innerText = 'QR-Code erfasst!';

            // Beispiel: Die URL hat das Format: 
            // https://fdm-mini-training-main-e9c4ja.laravel.cloud/quiz?option=A&token=XXXXX
            // Mit extractToken extrahieren wir den "token"-Parameter.
            const token = extractToken(decodedText);

            // Sende per AJAX den Vote an den Endpunkt zum Speichern.
            fetch("{{ route('vote.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    // Hier kannst du z. B. auch die question_id dynamisch setzen;
                    // In diesem Beispiel wird zur Demonstration question_id = 1 genutzt.
                    question_id: 1,
                    token: token,
                    group_identifier: "AdminScan" // oder eine dynamisch erfasste Gruppenkennung
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log("Antwort vom Server:", data);
                document.getElementById('message').innerText = 'Stimme gespeichert!';
            })
            .catch(error => {
                console.error("Fehler:", error);
                document.getElementById('message').innerText = 'Fehler beim Speichern!';
            });
        }

        // Bei Scan-Fehlern (nicht kritisch, z. B. wenn kein QR-Code erkannt wird)
        function onScanFailure(error) {
            console.warn(`QR-Code Scan Fehler: ${error}`);
        }

        // Hilfsfunktion zum Extrahieren des "token"-Parameters aus der URL.
        function extractToken(url) {
            const parts = url.split('?');
            if (parts.length < 2) {
                return null;
            }
            const params = new URLSearchParams(parts[1]);
            return params.get('token');
        }

        // Den Scanner starten.
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    </script>
</body>
</html>
