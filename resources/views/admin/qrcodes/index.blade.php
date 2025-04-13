<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>QR Codes für Antwortzettel</title>
    <style>
        /* Druck- und Bildschirmformatierung */
        body {
            font-family: Arial, sans-serif;
        }
        h1 {
            text-align: center;
        }
        .container {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .answer-sheet {
            width: 48%; /* Zwei Zettel pro Zeile – mit kleinem Abstand */
            height: 148mm;
            margin: 1%;
            border: 1px solid #000;
            box-sizing: border-box;
            padding: 10mm;
            position: relative;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10mm;
        }
        .header .logo {
            max-width: 50px; /* Größe der Logos anpassen */
            max-height: 50px;
        }
        .training-title {
            flex-grow: 1;
            text-align: center;
            font-size: 12px; /* Schriftgröße anpassen, damit sie dezent wirkt */
            margin: 0 10px;
        }
        .info {
            position: absolute;
            top: 70mm; /* Positionierung so wählen, dass der Header oben bleibt */
            left: 10mm;
            font-size: 16px;
        }
        .qr-code {
            position: absolute;
            bottom: 10mm;
            right: 10mm;
        }
        @media print {
            .answer-sheet {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <h1>QR Codes – Antwortzettel erstellen und ausdrucken</h1>
    <div class="container">
        @foreach($groups as $group)
            @foreach($group->qrCodes as $qr)
                <div class="answer-sheet">
                    <div class="header">
                        <div class="logo">
                            <img src="{{ asset('images/fhp-logo.png') }}" alt="FHP Logo" width="50px">
                        </div>
                        <div class="training-title">
                            <strong>FDM-Mini-Training 1:</strong><br>
                            Forschungsdaten &amp; Forschungsdatenmanagement
                        </div>
                        <div class="logo">
                            <img src="{{ asset('images/gfz-logo-en.png') }}" alt="GFZ Logo" width="60px">
                        </div>
                    </div>
                    <div class="info">
                        <strong>Gruppe:</strong> {{ $group->id }}<br>
                        <strong>Option:</strong> {{ $qr->letter }}
                    </div>
                    <div class="qr-code">
                        {!! QrCode::size(375)->generate("Group: {$group->id}, Option: {$qr->letter}") !!}
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>
</body>
</html>
