<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>FDM-Mini-Training Quiz</title>
    <!-- Tailwind CSS (via CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-4xl mx-auto p-6">
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h1 class="text-3xl font-bold text-center mb-6">FDM-Mini-Training Quiz</h1>
            <div id="quiz-container">
                <!-- Hier wird die Frage dynamisch eingeblendet -->
            </div>
            <div class="mt-6 text-center">
                <button id="next-button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Weiter
                </button>
            </div>
        </div>
    </div>

    <script>
        // Die per Controller geladene Liste der Fragen
        const questions = @json($questions);
        let currentQuestionIndex = 0;
        let pollInterval;

        // Rendert die aktuelle Frage samt Antwortoptionen
        function renderQuestion() {
            if (currentQuestionIndex >= questions.length) {
                // Quiz beendet – Weiterleitung zur Auswertungsseite
                window.location.href = "{{ route('quiz.summary') }}";
                return;
            }

            const currentQuestion = questions[currentQuestionIndex];
            const container = document.getElementById('quiz-container');
            container.innerHTML = '';

            // Frage
            const questionTitle = document.createElement('h2');
            questionTitle.className = "text-2xl font-semibold mb-4";
            questionTitle.textContent = currentQuestion.question_text;
            container.appendChild(questionTitle);

            // Antwortoptionen
            const optionsList = document.createElement('div');
            optionsList.className = "space-y-4";
            currentQuestion.options.forEach(option => {
                const optionDiv = document.createElement('div');
                optionDiv.className = "p-4 bg-gray-50 rounded shadow flex items-center justify-between";
                
                const optionText = document.createElement('span');
                optionText.className = "font-medium";
                optionText.textContent = `${option.letter}: ${option.option_text}`;
                
                // Platzhalter für die Live-Stimmen (initial 0)
                const voteCount = document.createElement('span');
                voteCount.className = "text-xl font-bold text-blue-600";
                voteCount.id = `vote-${option.letter}`;
                voteCount.textContent = "0";

                optionDiv.appendChild(optionText);
                optionDiv.appendChild(voteCount);
                optionsList.appendChild(optionDiv);
            });
            container.appendChild(optionsList);

            // Starte die Live-Aktualisierung der Ergebnisse
            startPolling(currentQuestion.id);
        }

        // Holt per AJAX die aktuellen Scan-Ergebnisse für die gegebene Frage
        function startPolling(questionId) {
            if (pollInterval) clearInterval(pollInterval);
            pollInterval = setInterval(() => {
                fetch("{{ url('/quiz/results') }}/" + questionId)
                .then(response => response.json())
                .then(data => {
                    // Aktualisiere die Stimmenanzeige für jede Option
                    for (const letter in data) {
                        const voteElement = document.getElementById('vote-' + letter);
                        if (voteElement) {
                            voteElement.textContent = data[letter];
                        }
                    }
                })
                .catch(error => console.error("Fehler beim Abrufen der Ergebnisse:", error));
            }, 2000); // alle 2 Sekunden aktualisieren
        }

        // Bei Klick auf "Weiter" wird zur nächsten Frage gewechselt
        document.getElementById('next-button').addEventListener('click', () => {
            if (pollInterval) clearInterval(pollInterval);
            currentQuestionIndex++;
            renderQuestion();
        });

        // Zeige initial die erste Frage an
        renderQuestion();
    </script>
</body>
</html>
