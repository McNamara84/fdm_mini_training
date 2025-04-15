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
            <!-- Navigationsbereich mit drei Buttons:
                 "Zurück", "Antworten anzeigen" (zweistufig) und "Weiter" -->
            <div class="mt-6 flex justify-center items-center space-x-4">
                <button id="back-button" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Zurück
                </button>
                <button id="show-results-button" class="hidden bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Antworten anzeigen
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
        let currentResults = {}; // Hier werden die abgefragten Ergebnisse gespeichert
        // globaler Status für den "show-results-button":
        // 0: noch nicht geklickt, 1: erste Klick (Ergebnisse einblenden, Buttontext ändern)
        let showResultsButtonState = 0;

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

        // Rendert die aktuelle Frage samt Antwortoptionen.
        // Zusätzlich wird in jeder Option per data-Attribut gespeichert, ob sie korrekt ist.
        function renderQuestion() {
            if (currentQuestionIndex >= questions.length) {
                window.location.href = "{{ route('quiz.summary') }}";
                return;
            }
            // Reset des Button-Status, sobald eine neue Frage angezeigt wird
            showResultsButtonState = 0;
            document.getElementById('show-results-button').classList.add('hidden');
            document.getElementById('show-results-button').textContent = "Antworten anzeigen";

            const currentQuestion = questions[currentQuestionIndex];
            const container = document.getElementById('quiz-container');
            container.innerHTML = '';

            // Frageanzeige
            const questionTitle = document.createElement('h2');
            questionTitle.className = "text-2xl font-semibold mb-4";
            questionTitle.textContent = currentQuestion.question_text;
            container.appendChild(questionTitle);

            // Badge "Fertig!" (wird eingeblendet, wenn alle 6 Stimmen vorliegen)
            const badge = document.createElement('div');
            badge.id = "ready-badge";
            badge.className = "hidden inline-block bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded-full mb-4";
            badge.textContent = "Fertig!";
            container.appendChild(badge);

            // Antwortoptionen: Erstelle für jede Option ein Card-Element und speichere per data-Attribut, ob sie richtig ist
            const optionsList = document.createElement('div');
            optionsList.className = "space-y-4";
            currentQuestion.options.forEach(option => {
                const optionDiv = document.createElement('div');
                optionDiv.className = "option-card p-4 bg-gray-50 rounded shadow flex items-center justify-between";
                // Speichere ob die Option korrekt ist, als data-Attribut
                optionDiv.setAttribute('data-correct', option.is_correct ? "true" : "false");

                const optionText = document.createElement('span');
                optionText.className = "font-medium";
                optionText.textContent = `${option.letter}: ${option.option_text}`;

                // Vote-Count: initial leer und unsichtbar
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

            // Button "Zurück" deaktivieren, wenn wir bei der ersten Frage sind
            document.getElementById('back-button').disabled = (currentQuestionIndex === 0);

            // Aktualisiere den aktiven Fragensatz zentral
            setActiveQuestion(currentQuestion.id);

            // Starte das Polling der Live-Ergebnisse für diese Frage
            startPolling(currentQuestion.id);
        }

        // Holt per AJAX die aktuellen Scan-Ergebnisse für die gegebene Frage
        // Speichert die Ergebnisse in currentResults und zeigt den "Antworten anzeigen"-Button, wenn alle 6 Stimmen abgegeben sind.
        function startPolling(questionId) {
            if (pollInterval) clearInterval(pollInterval);
            pollInterval = setInterval(() => {
                fetch("{{ url('/quiz/results') }}/" + questionId)
                    .then(response => response.json())
                    .then(data => {
                        let totalVotes = 0;
                        for (const letter in data) {
                            totalVotes += parseInt(data[letter]);
                        }
                        if (totalVotes >= 6) {
                            // Ergebnisse werden in globaler Variable gespeichert
                            currentResults = data;
                            // Zeige den "Antworten anzeigen"-Button (falls noch nicht sichtbar)
                            document.getElementById('show-results-button').classList.remove('hidden');
                            // Blende den "Fertig!"-Badge ein
                            document.getElementById('ready-badge').classList.remove('hidden');
                        } else {
                            // Wenn noch nicht alle Antworten da sind: Button ausblenden
                            document.getElementById('show-results-button').classList.add('hidden');
                            // Setze alle Vote-Anzeigen zurück und unsichtbar
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

        // Event Listener für den Button "Antworten anzeigen" / "Lösung anzeigen"
        document.getElementById('show-results-button').addEventListener('click', () => {
            if (showResultsButtonState === 0) {
                // Erster Klick: Zeige die abgefragten Stimmen in den Vote-Elementen an
                for (const letter in currentResults) {
                    const voteElement = document.getElementById('vote-' + letter);
                    if (voteElement) {
                        voteElement.textContent = currentResults[letter];
                        voteElement.style.visibility = 'visible';
                    }
                }
                // Ändere den Buttontext zu "Lösung anzeigen" und aktualisiere den Status
                document.getElementById('show-results-button').textContent = "Lösung anzeigen";
                showResultsButtonState = 1;
            } else if (showResultsButtonState === 1) {
                // Zweiter Klick: Färbe die Antwortkarten ein – grün für richtig, rot für falsch
                const optionCards = document.querySelectorAll('.option-card');
                optionCards.forEach(card => {
                    const isCorrect = card.getAttribute('data-correct') === "true";
                    if (isCorrect) {
                        card.classList.remove('bg-gray-50');
                        card.classList.add('bg-green-200');
                    } else {
                        card.classList.remove('bg-gray-50');
                        card.classList.add('bg-red-200');
                    }
                });
                // Nach Anzeige der Lösung kann der Button auch deaktiviert oder ausgeblendet werden
                document.getElementById('show-results-button').disabled = true;
                // Optional: Alternativ den Button ausblenden:
                // document.getElementById('show-results-button').classList.add('hidden');
            }
        });

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
