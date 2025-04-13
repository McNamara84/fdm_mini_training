<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>FDM-Mini-Training 1</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-5xl mx-auto p-6">
        <!-- Header mit Logos -->
        <div class="flex items-center justify-between mb-6">
            <img src="{{ asset('images/fhp-logo.png') }}" alt="FHP Logo" class="h-6">
            <h1 class="text-3xl font-bold text-center">FDM-Mini-Training 1</h1>
            <img src="{{ asset('images/gfz-logo-en.jpg') }}" alt="GFZ Logo" class="h-6">
        </div>
        <div class="bg-white shadow-lg rounded-lg p-6">
            <div id="quiz-container">
                <!-- Dynamischer Inhalt: Frage und Antwortoptionen -->
            </div>
            <div class="mt-6 flex justify-between">
                <!-- Back Button: Nur anzeigen, wenn nicht bei der ersten Frage -->
                <button id="back-button" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Zurück
                </button>
                <button id="next-button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Weiter
                </button>
            </div>
        </div>
    </div>

    <script>
        // Liste der Fragen, die vom Controller als JSON übergeben wurde
        const questions = @json($questions);
        let currentQuestionIndex = 0;
        let pollInterval;

        // Rendert die aktuelle Frage samt Antwortoptionen und Badge, falls alle Antworten vorhanden sind.
        function renderQuestion() {
            if (currentQuestionIndex >= questions.length) {
                // Falls alle Fragen abgearbeitet sind, leite zur Zusammenfassungsseite weiter.
                window.location.href = "{{ route('quiz.summary') }}";
                return;
            }
            const currentQuestion = questions[currentQuestionIndex];
            const container = document.getElementById('quiz-container');
            container.innerHTML = '';

            // Frageanzeige
            const questionTitle = document.createElement('h2');
            questionTitle.className = "text-2xl font-semibold mb-4";
            questionTitle.textContent = currentQuestion.question_text;
            container.appendChild(questionTitle);

            // Badge "Fertig!" (wird später anhand der Scans gesetzt)
            const badge = document.createElement('div');
            badge.id = "ready-badge";
            badge.className = "hidden inline-block bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded-full mb-4";
            badge.textContent = "Fertig!";
            container.appendChild(badge);

            // Antwortoptionen
            const optionsList = document.createElement('div');
            optionsList.className = "space-y-4";
            currentQuestion.options.forEach(option => {
                const optionDiv = document.createElement('div');
                optionDiv.className = "p-4 bg-gray-50 rounded shadow flex items-center justify-between";
                
                const optionText = document.createElement('span');
                optionText.className = "font-medium";
                optionText.textContent = `${option.letter}: ${option.option_text}`;
                
                // Platzhalter für Live-Zählung
                const voteCount = document.createElement('span');
                voteCount.className = "text-xl font-bold text-blue-600";
                voteCount.id = `vote-${option.letter}`;
                voteCount.textContent = "0";

                optionDiv.appendChild(optionText);
                optionDiv.appendChild(voteCount);
                optionsList.appendChild(optionDiv);
            });
            container.appendChild(optionsList);

            // Aktualisiere den Status des Back-Buttons
            document.getElementById('back-button').disabled = (currentQuestionIndex === 0);

            // Starte das Polling der Live-Ergebnisse
            startPolling(currentQuestion.id);
        }

        // Holt per AJAX die aktuellen Scan-Ergebnisse für die gegebene Frage und aktualisiert die Anzeige.
        function startPolling(questionId) {
            if (pollInterval) clearInterval(pollInterval);
            pollInterval = setInterval(() => {
                fetch("{{ url('/quiz/results') }}/" + questionId)
                .then(response => response.json())
                .then(data => {
                    let totalVotes = 0;
                    for (const letter in data) {
                        totalVotes += parseInt(data[letter]);
                        const voteElement = document.getElementById('vote-' + letter);
                        if (voteElement) voteElement.textContent = data[letter];
                    }
                    // Falls alle 6 Antworten erfasst wurden, Badge "Fertig!" anzeigen.
                    if (totalVotes >= 6) {
                        document.getElementById('ready-badge').classList.remove('hidden');
                    } else {
                        document.getElementById('ready-badge').classList.add('hidden');
                    }
                })
                .catch(error => console.error("Fehler beim Abrufen der Ergebnisse:", error));
            }, 2000); // Aktualisierung alle 2 Sekunden
        }

        // Event Listener für "Weiter"
        document.getElementById('next-button').addEventListener('click', () => {
            if (pollInterval) clearInterval(pollInterval);
            currentQuestionIndex++;
            renderQuestion();
        });

        // Event Listener für "Zurück"
        document.getElementById('back-button').addEventListener('click', () => {
            if (currentQuestionIndex > 0) {
                if (pollInterval) clearInterval(pollInterval);
                currentQuestionIndex--;
                renderQuestion();
            }
        });

        // Initiale Anzeige der ersten Frage
        renderQuestion();
    </script>
</body>
</html>
