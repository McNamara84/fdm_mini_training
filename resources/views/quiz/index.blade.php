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
            <img src="{{ asset('images/gfz-logo-en.png') }}" alt="GFZ Logo" class="h-6">
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

        // Aktualisiert den aktiven Fragensatz im Backend
        function setActiveQuestion(questionId) {
            fetch("{{ route('quiz.active.update') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ quiz_question_id: questionId })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    console.log("Aktiver Fragewert aktualisiert auf: " + questionId);
                }
            })
            .catch(error => console.error("Fehler beim Aktualisieren des aktiven Fragewerts:", error));
        }

        // Rendert die aktuelle Frage samt Antwortoptionen und einem "Fertig!" Badge
        // Die Abstimmungsergebnisse (Vote-Zählung) werden erst angezeigt, wenn alle 6 Stimmen vorliegen.
        function renderQuestion() {
            if (currentQuestionIndex >= questions.length) {
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

            // Badge "Fertig!" (wird eingeblendet, wenn alle 6 Antworten vorliegen)
            const badge = document.createElement('div');
            badge.id = "ready-badge";
            badge.className = "hidden inline-block bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded-full mb-4";
            badge.textContent = "Fertig!";
            container.appendChild(badge);

            // Antwortoptionen (Zellen, in denen die Anzahl der Stimmen angezeigt wird)
            const optionsList = document.createElement('div');
            optionsList.className = "space-y-4";
            currentQuestion.options.forEach(option => {
                const optionDiv = document.createElement('div');
                optionDiv.className = "p-4 bg-gray-50 rounded shadow flex items-center justify-between";

                const optionText = document.createElement('span');
                optionText.className = "font-medium";
                optionText.textContent = `${option.letter}: ${option.option_text}`;

                // Der Vote-Count (initial leer und nicht sichtbar)
                const voteCount = document.createElement('span');
                voteCount.className = "text-xl font-bold text-blue-600";
                voteCount.id = `vote-${option.letter}`;
                voteCount.textContent = "";
                voteCount.style.visibility = 'hidden';

                optionDiv.appendChild(optionText);
                optionDiv.appendChild(voteCount);
                optionsList.appendChild(optionDiv);
            });
            container.appendChild(optionsList);

            // Update des "Zurück"-Buttons (deaktivieren, wenn erste Frage)
            document.getElementById('back-button').disabled = (currentQuestionIndex === 0);

            // Aktualisiere den aktiven Fragensatz zentral
            setActiveQuestion(currentQuestion.id);

            // Starte das Polling der Live-Ergebnisse
            startPolling(currentQuestion.id);
        }

        // Holt per AJAX die aktuellen Scan-Ergebnisse für die gegebene Frage
        function startPolling(questionId) {
            if (pollInterval) clearInterval(pollInterval);
            pollInterval = setInterval(() => {
                fetch("{{ url('/quiz/results') }}/" + questionId)
                    .then(response => response.json())
                    .then(data => {
                        let totalVotes = 0;
                        // Berechne die Gesamtzahl der abgegebenen Stimmen
                        for (const letter in data) {
                            totalVotes += parseInt(data[letter]);
                        }
                        // Wenn alle 6 Gruppen abgestimmt haben, werden die Stimmen sichtbar gesetzt
                        if (totalVotes >= 6) {
                            for (const letter in data) {
                                const voteElement = document.getElementById('vote-' + letter);
                                if (voteElement) {
                                    voteElement.textContent = data[letter];
                                    voteElement.style.visibility = 'visible';
                                }
                            }
                            document.getElementById('ready-badge').classList.remove('hidden');
                        } else {
                            // Vorher: Stimmen werden nicht angezeigt
                            for (const letter in data) {
                                const voteElement = document.getElementById('vote-' + letter);
                                if (voteElement) {
                                    voteElement.textContent = "";
                                    voteElement.style.visibility = 'hidden';
                                }
                            }
                            document.getElementById('ready-badge').classList.add('hidden');
                        }
                    })
                    .catch(error => console.error("Fehler beim Abrufen der Ergebnisse:", error));
            }, 2000);
        }

        // Event Listener: "Weiter"
        document.getElementById('next-button').addEventListener('click', () => {
            if (pollInterval) clearInterval(pollInterval);
            currentQuestionIndex++;
            renderQuestion();
        });

        // Event Listener: "Zurück"
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
