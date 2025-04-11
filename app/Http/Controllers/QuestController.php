<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;

class QuestController extends Controller
{
    /**
     * Zeigt die Startseite des Trainings an.
     */
    public function showWelcome()
    {
        return view('welcome');
    }

    /**
     * Zeigt die Quizseite an.
     * Es wird die erste Frage mit Typ "quiz" angezeigt.
     */
    public function showQuiz()
    {
        $question = Question::where('type', 'quiz')->orderBy('order')->first();

        return view('quiz', compact('question'));
    }

    /**
     * Zeigt die interaktive Storyseite an.
     * Es wird die erste Frage mit Typ "story" angezeigt.
     */
    public function showStory()
    {
        $question = Question::where('type', 'story')->orderBy('order')->first();

        return view('story', compact('question'));
    }

    /**
     * Zeigt die Zusammenfassungsseite an, die alle aggregierten Ergebnisse anzeigt.
     */
    public function showSummary()
    {
        $questions = Question::with('options.votes')->orderBy('order')->get();

        return view('summary', compact('questions'));
    }
}
