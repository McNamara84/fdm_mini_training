<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mini-Training Zusammenfassung</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Zusammenfassung der Ergebnisse</h1>
        @foreach($questions as $question)
            <div class="mb-6 bg-white p-4 rounded shadow">
                <h2 class="text-2xl font-semibold mb-2">{{ $question->question_text }}</h2>
                <ul>
                    @foreach($question->options as $option)
                        <li class="mb-1">
                            <strong>{{ $option->option_letter }}:</strong> {{ $option->option_text }}
                            – Stimmen: {{ $option->votes->count() }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
        <p class="text-lg font-semibold">Take-Home Message: Gutes FDM fängt mit klaren Entscheidungen an!</p>
    </div>
</body>
</html>
