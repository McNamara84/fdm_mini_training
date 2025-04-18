<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Training Auswertung</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="max-w-5xl mx-auto p-6">
        {{-- Header with institution logos and summary title --}}
        <div class="flex items-center justify-between mb-6">
            <img src="{{ asset('images/fhp-logo.png') }}" alt="FHP Logo" class="h-16">
            <h1 class="text-3xl font-bold text-center">Training 1: Auswertung</h1>
            <img src="{{ asset('images/gfz-logo-en.png') }}" alt="GFZ Logo" class="h-16">
        </div>

        <div class="bg-white shadow-lg rounded-lg p-6">
            {{-- Loop through each question and display its results --}}
            @foreach($questions as $question)
                    <div class="mb-8 border-b pb-4">
                        {{-- Question text --}}
                        <h2 class="text-2xl font-semibold mb-2">{{ $question->question_text }}</h2>

                        <div class="space-y-2">
                            {{-- Loop through options and show count and correctness --}}
                            @foreach($question->options as $option)
                                            @php
                                                $result = $question->results->firstWhere('letter', $option->letter);
                                                $count = $result ? $result->count : 0;
                                            @endphp
                                            <div class="p-4 bg-gray-50 rounded shadow flex items-center justify-between">
                                                {{-- Option text and correct marker --}}
                                                <span class="font-medium">
                                                    {{ $option->letter }}: {{ $option->option_text }}
                                                    @if($option->is_correct)
                                                        <span class="text-green-600">(richtig)</span>
                                                    @endif
                                                </span>
                                                {{-- Display vote count --}}
                                                <span class="text-xl font-bold text-blue-600">{{ $count }}</span>
                                            </div>
                            @endforeach
                        </div>
                    </div>
            @endforeach

            {{-- Navigation: back to quiz and reset quiz --}}
            <div class="flex justify-between mt-6">
                <a href="{{ route('quiz.index') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Zurück
                </a>
                <form action="{{ route('quiz.reset') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Quiz zurücksetzen
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>