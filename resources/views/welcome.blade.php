<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>DataQuest Interactive – Willkommen</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-xl mx-auto">
        <h1 class="text-3xl font-bold mb-4">Forschungsdaten & Forschungsdatenmanagement</h1>
        <p class="mb-4">
            Willkommen zu unserem interaktiven FDM-Mini-Training! Ihr werdet in 5 Gruppen eingeteilt.
            Jede Gruppe erhält gedruckte Antwortzettel mit QR-Codes für die Abstimmung.
            Anschließend scannen wir als Trainer die Codes mit unserem Smartphone – so fließen eure Gruppenergebnisse live in die Auswertung ein.
        </p>
        <p class="mb-4">
            Startet jetzt in das Quiz:
            <a href="{{ route('quiz') }}" class="text-blue-500 underline">Zum Quiz</a>
        </p>
    </div>
</body>
</html>
