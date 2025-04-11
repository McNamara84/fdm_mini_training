<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Dashboard - DataQuest</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- Optional: Einbindung von JavaScript-Bibliotheken (z.B. Livewire, Alpine.js) für Live-Updates -->
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Live-Auswertung (Admin-Dashboard)</h1>
        @foreach($questions as $question)
            <div class="mb-6 bg-white p-4 rounded shadow">
                <h2 class="text-2xl font-semibold mb-2">{{ $question->question_text }}</h2>
                <table class="w-full table-auto">
                    <thead>
                        <tr>
                            <th class="border px-4 py-2">Option</th>
                            <th class="border px-4 py-2">Beschreibung</th>
                            <th class="border px-4 py-2">Stimmen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($question->options as $option)
                            <tr>
                                <td class="border px-4 py-2">{{ $option->option_letter }}</td>
                                <td class="border px-4 py-2">{{ $option->option_text }}</td>
                                <td class="border px-4 py-2">{{ $option->votes->count() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
</body>
</html>
