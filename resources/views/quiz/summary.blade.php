<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Quiz Auswertung</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-4xl mx-auto p-6">
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h1 class="text-3xl font-bold text-center mb-6">Quiz Auswertung</h1>
            @foreach($questions as $question)
                <div class="mb-8 border-b pb-4">
                    <h2 class="text-2xl font-semibold mb-2">{{ $question->question_text }}</h2>
                    <div class="space-y-2">
                        @foreach($question->options as $option)
                            @php
                                $result = $question->results->firstWhere('letter', $option->letter);
                                $count = $result ? $result->count : 0;
                            @endphp
                            <div class="p-4 bg-gray-50 rounded shadow flex items-center justify-between">
                                <span class="font-medium">
                                    {{ $option->letter }}: {{ $option->option_text }}
                                    @if($option->is_correct)
                                        <span class="text-green-600">(richtig)</span>
                                    @endif
                                </span>
                                <span class="text-xl font-bold text-blue-600">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>
