<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mini-training Quiz-Einführung</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Quizfrage</h1>
        <div class="mb-4 bg-white p-4 rounded shadow">
            <p class="text-lg">{{ $question->question_text }}</p>
        </div>
        <div class="mb-4">
            <h2 class="text-xl font-semibold mb-2">Antwortoptionen:</h2>
            <ul>
                @foreach($question->options as $option)
                    <li class="mb-2">
                        <strong>{{ $option->option_letter }}:</strong> {{ $option->option_text }}
                    </li>
                @endforeach
            </ul>
        </div>
        <p class="text-sm italic">
            Hinweis: Die gedruckten QR-Code-Zettel entsprechen den Antwortoptionen.
        </p>
        <div class="mt-6">
            <a href="{{ route('story') }}" class="px-4 py-2 bg-blue-500 text-white rounded">Weiter zur interaktiven Story</a>
        </div>
    </div>
</body>
</html>
