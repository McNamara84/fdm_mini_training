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
        function extractParams(url) {
            const parts = url.split('?');
            if (parts.length < 2) {
                return null;
            }
            const params = new URLSearchParams(parts[1]);
            return {
                token: params.get('token'),
                question_id: params.get('question_id'), // neu
                option: params.get('option')
            };
        }

        function onScanSuccess(decodedText, decodedResult) {
            console.log(`QR-Code gescannt: ${decodedText}`);
            document.getElementById('message').innerText = 'QR-Code erfasst!';

            const params = extractParams(decodedText);
            if (!params || !params.token || !params.question_id) {
                document.getElementById('message').innerText = 'Ungültiger QR-Code!';
                return;
            }

            fetch("{{ route('vote.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    question_id: parseInt(params.question_id),
                    token: params.token,
                    group_identifier: "AdminScan"
                })
            })
                .then(response => response.json())
                .then(data => {
                    console.log("Antwort vom Server:", data);
                    if (data.success) {
                        document.getElementById('message').innerText = 'Stimme gespeichert!';
                    } else {
                        document.getElementById('message').innerText = 'Fehler beim Speichern!';
                    }
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