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
        {{-- Header section with logos and training title --}}
        <div class="flex items-center justify-between mb-6">
            <img src="{{ asset('images/fhp-logo.png') }}" alt="FHP Logo" class="h-6">
            <h1 class="text-3xl font-bold text-center">FDM-Mini-Training 1</h1>
            <img src="{{ asset('images/gfz-logo-en.png') }}" alt="GFZ Logo" class="h-6">
        </div>
        <div class="bg-white shadow-lg rounded-lg p-6">
            {{-- Container where the current quiz question and its options will be dynamically rendered --}}
            <div id="quiz-container"></div>

            {{-- Navigation buttons: Back, Show Results (with countdown), Next --}}
            <div class="mt-6 flex justify-center items-center space-x-4">
                <button id="back-button" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Zurück
                </button>
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
        // Parsed list of quiz questions from the backend
        const questions = @json($questions);
        let currentQuestionIndex = 0;
        let pollInterval;
        let countdownInterval;
        let currentResults = {};
        let showResultsButtonState = 0; // 0 = before showing results, 1 = results visible

        // Update the currently active question in the backend
        function setActiveQuestion(questionId) {
            fetch("{{ route('quiz.active.update') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ quiz_question_id: questionId })
            }).catch(error => console.error("Error updating active question:", error));
        }

        // Render the question, reset countdown, and start polling for results
        function renderQuestion() {
            if (currentQuestionIndex >= questions.length) {
                // Redirect to summary when all questions are done
                window.location.href = "{{ route('quiz.summary') }}";
                return;
            }

            // Reset show-results button and countdown
            showResultsButtonState = 0;
            const showBtn = document.getElementById('show-results-button');
            showBtn.disabled = true;
            showBtn.textContent = "00:60";
            if (countdownInterval) clearInterval(countdownInterval);

            // Start 60-second countdown timer
            let timeLeft = 60;
            countdownInterval = setInterval(() => {
                timeLeft--;
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                showBtn.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                if (timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    showBtn.textContent = "Antworten anzeigen";
                    showBtn.disabled = false;
                }
            }, 1000);

            const question = questions[currentQuestionIndex];
            const container = document.getElementById('quiz-container');
            container.innerHTML = '';

            // Display question text
            const title = document.createElement('h2');
            title.className = "text-2xl font-semibold mb-4";
            title.textContent = question.question_text;
            container.appendChild(title);

            // 'Ready!' badge hidden until all votes are in
            const badge = document.createElement('div');
            badge.id = "ready-badge";
            badge.className = "hidden inline-block bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded-full mb-4";
            badge.textContent = "Fertig!";
            container.appendChild(badge);

            // Render each answer option card
            const optionsBlock = document.createElement('div');
            optionsBlock.className = "space-y-4";
            question.options.forEach(opt => {
                const card = document.createElement('div');
                card.className = "option-card p-4 bg-gray-50 rounded shadow flex items-center justify-between";
                card.setAttribute('data-correct', opt.is_correct);

                const textSpan = document.createElement('span');
                textSpan.className = "font-medium";
                textSpan.textContent = `${opt.letter}: ${opt.option_text}`;

                const countSpan = document.createElement('span');
                countSpan.id = `vote-${opt.letter}`;
                countSpan.className = "text-xl font-bold text-blue-600";
                countSpan.style.visibility = 'hidden';

                card.appendChild(textSpan);
                card.appendChild(countSpan);
                optionsBlock.appendChild(card);
            });
            container.appendChild(optionsBlock);

            // Disable back button on the first question
            document.getElementById('back-button').disabled = (currentQuestionIndex === 0);
            setActiveQuestion(question.id);
            startPolling(question.id);
        }

        // Poll the backend every 2 seconds for live scan counts
        function startPolling(questionId) {
            if (pollInterval) clearInterval(pollInterval);
            pollInterval = setInterval(() => {
                fetch(`{{ url('/quiz/results') }}/${questionId}`)
                    .then(res => res.json())
                    .then(data => {
                        const totalVotes = Object.values(data).reduce((sum, v) => sum + parseInt(v), 0);
                        if (totalVotes >= 6) {
                            currentResults = data;
                            document.getElementById('ready-badge').classList.remove('hidden');
                            const btn = document.getElementById('show-results-button');
                            if (btn.disabled) {
                                clearInterval(countdownInterval);
                                btn.textContent = "Antworten anzeigen";
                                btn.disabled = false;
                            }
                        }
                    }).catch(error => console.error("Error fetching results:", error));
            }, 2000);
        }

        // Handle show-results button click: first show counts, then reveal correct answer
        document.getElementById('show-results-button').addEventListener('click', () => {
            const btn = document.getElementById('show-results-button');
            if (showResultsButtonState === 0) {
                // Show vote counts
                Object.entries(currentResults).forEach(([letter, cnt]) => {
                    const voteEl = document.getElementById(`vote-${letter}`);
                    voteEl.textContent = cnt;
                    voteEl.style.visibility = 'visible';
                });
                btn.textContent = "Lösung anzeigen";
                showResultsButtonState = 1;
            } else {
                // Highlight correct vs incorrect options
                document.querySelectorAll('.option-card').forEach(card => {
                    const correct = card.getAttribute('data-correct') === 'true';
                    card.classList.toggle('bg-green-200', correct);
                    card.classList.toggle('bg-red-200', !correct);
                });
                btn.disabled = true;
            }
        });

        // Next and Back button event handlers
        document.getElementById('next-button').addEventListener('click', () => {
            if (pollInterval) clearInterval(pollInterval);
            currentQuestionIndex++;
            renderQuestion();
        });
        document.getElementById('back-button').addEventListener('click', () => {
            if (currentQuestionIndex > 0) {
                if (pollInterval) clearInterval(pollInterval);
                currentQuestionIndex--;
                renderQuestion();
            }
        });

        // Initial render on page load
        renderQuestion();
    </script>
</body>

</html>