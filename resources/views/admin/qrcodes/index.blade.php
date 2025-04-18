<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>QR Codes für Antwortzettel</title>
    <style>
        /* Print and screen formatting */
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
            width: 48%;
            /* Two sheets per row with small gap */
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
            max-width: 50px;
            /* Adjust logo sizes */
            max-height: 50px;
        }

        .training-title {
            flex-grow: 1;
            text-align: center;
            font-size: 12px;
            /* Subtle font size */
            margin: 0 10px;
        }

        .info {
            position: absolute;
            top: 70mm;
            /* Keep header fixed at top */
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
    {{-- Heading for the print view --}}
    <h1>QR Codes – Antwortzettel erstellen und ausdrucken</h1>

    <div class="container">
        {{-- Loop through each group and its QR codes to generate sheets --}}
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

                    {{-- Display group ID and option letter --}}
                    <div class="info">
                        <strong>Gruppe:</strong> {{ $group->id }}<br>
                        <strong>Option:</strong> {{ $qr->letter }}
                    </div>

                    {{-- Generate the QR code with content: Group: X, Option: Y --}}
                    <div class="qr-code">
                        {!! QrCode::size(375)->generate("Group: {$group->id}, Option: {$qr->letter}") !!}
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>
</body>

</html>