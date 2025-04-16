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
            <!-- Navigationsbereich mit drei Buttons: "Zurück", "Antworten anzeigen" (mit Countdown) und "Weiter" -->
            <div class="mt-6 flex justify-center items-center space-x-4">
                <button id="back-button" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Zurück
                </button>
                <!-- Button ist von Anfang an sichtbar, aber deaktiviert und zeigt den Countdown -->
                <button id="show-results-button" disabled class="bg-green-500 text-white font-bold py-2 px-4 rounded">
                    00:60
                </button>
                <button id="next-button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Weiter
                </button>
            </div>
        </div>
    </div>

    <script>
        // Globale Variablen
        const questions = @json($questions);
        let currentQuestionIndex = 0;
        let pollInterval;
        let countdownInterval;
        let currentResults = {}; // Zwischenspeicherung der abgefragten Ergebnisse
        // Status für den "show-results-button": 0 = initial, 1 = Ergebnisse sichtbar
        let showResultsButtonState = 0;
    
        // Aktualisiert den aktiven Fragensatz im Backend (optional)
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
    
        // Rendert die aktuelle Frage, setzt den Countdown zurück und startet das Polling
        function renderQuestion() {
            if (currentQuestionIndex >= questions.length) {
                window.location.href = "{{ route('quiz.summary') }}";
                return;
            }
            // Reset des Button-Status für die aktuelle Frage
            showResultsButtonState = 0;
            const showResultsButton = document.getElementById('show-results-button');
            showResultsButton.disabled = true;
            // Setze den Button-Text auf den Countdown-Startwert
            showResultsButton.textContent = "00:60";
            // Falls ein alter Countdown läuft, beenden
            if (countdownInterval) clearInterval(countdownInterval);
            
            // Starte den 60-Sekunden-Countdown
            let timeLeft = 60;
            countdownInterval = setInterval(() => {
                timeLeft--;
                let minutes = Math.floor(timeLeft / 60);
                let seconds = timeLeft % 60;
                showResultsButton.textContent = 
                    (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;
                if(timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    showResultsButton.textContent = "Antworten anzeigen";
                    showResultsButton.disabled = false;
                }
            }, 1000);
    
            const currentQuestion = questions[currentQuestionIndex];
            const container = document.getElementById('quiz-container');
            container.innerHTML = '';
    
            // Frageanzeige
            const questionTitle = document.createElement('h2');
            questionTitle.className = "text-2xl font-semibold mb-4";
            questionTitle.textContent = currentQuestion.question_text;
            container.appendChild(questionTitle);
    
            // "Fertig!"-Badge (wird eingeblendet, wenn alle 6 Stimmen vorliegen)
            const badge = document.createElement('div');
            badge.id = "ready-badge";
            badge.className = "hidden inline-block bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded-full mb-4";
            badge.textContent = "Fertig!";
            container.appendChild(badge);
    
            // Antwortoptionen anzeigen
            const optionsList = document.createElement('div');
            optionsList.className = "space-y-4";
            currentQuestion.options.forEach(option => {
                const optionDiv = document.createElement('div');
                optionDiv.className = "option-card p-4 bg-gray-50 rounded shadow flex items-center justify-between";
                optionDiv.setAttribute('data-correct', option.is_correct ? "true" : "false");
    
                const optionText = document.createElement('span');
                optionText.className = "font-medium";
                optionText.textContent = `${option.letter}: ${option.option_text}`;
    
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
    
            // "Zurück"-Button deaktivieren, wenn erste Frage
            document.getElementById('back-button').disabled = (currentQuestionIndex === 0);
            // Aktualisiere den aktiven Fragensatz
            setActiveQuestion(currentQuestion.id);
            // Starte das Polling für die Live-Ergebnisse
            startPolling(currentQuestion.id);
        }
    
        // Holt per AJAX die aktuellen Ergebnisse für die gegebene Frage
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
                    // Falls alle 6 Stimmen vorhanden sind, zeige das "Fertig!"-Badge und aktiviere den Button (falls noch nicht aktiviert)
                    if (totalVotes >= 6) {
                        currentResults = data;
                        document.getElementById('ready-badge').classList.remove('hidden');
                        const showResultsButton = document.getElementById('show-results-button');
                        if (showResultsButton.disabled) {
                            clearInterval(countdownInterval);
                            showResultsButton.textContent = "Antworten anzeigen";
                            showResultsButton.disabled = false;
                        }
                    }
                })
                .catch(error => console.error("Fehler beim Abrufen der Ergebnisse:", error));
            }, 2000);
        }
    
        // Event Listener für "Antworten anzeigen" / "Lösung anzeigen"
        document.getElementById('show-results-button').addEventListener('click', () => {
            if (showResultsButtonState === 0) {
                for (const letter in currentResults) {
                    const voteElement = document.getElementById('vote-' + letter);
                    if (voteElement) {
                        voteElement.textContent = currentResults[letter];
                        voteElement.style.visibility = 'visible';
                    }
                }
                document.getElementById('show-results-button').textContent = "Lösung anzeigen";
                showResultsButtonState = 1;
            } else if (showResultsButtonState === 1) {
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
                document.getElementById('show-results-button').disabled = true;
            }
        });
    
        // "Weiter"-Button Event Listener
        document.getElementById('next-button').addEventListener('click', () => {
            if (pollInterval) clearInterval(pollInterval);
            currentQuestionIndex++;
            renderQuestion();
        });
    
        // "Zurück"-Button Event Listener
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
