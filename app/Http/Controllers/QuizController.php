<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuizQuestion;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    /**
     * Zeigt das Quiz an – Startseite der Quiz-Anzeige (/quiz).
     */
    public function index()
    {
        $questions = QuizQuestion::with('options')->orderBy('sort_order')->get();
        return view('quiz.index', compact('questions'));
    }

    /**
     * Liefert per JSON die aktuellen Ergebnisse (Scan-Zählungen) für die übergebene Frage.
     */
    public function getResults($questionId)
    {
        $results = DB::table('scans')
            ->join('qr_codes', 'scans.qr_code_id', '=', 'qr_codes.id')
            ->select('qr_codes.letter', DB::raw('count(scans.id) as count'))
            ->where('scans.quiz_question_id', $questionId)
            ->groupBy('qr_codes.letter')
            ->get();

        // Standardwerte für alle Optionen: A-D
        $data = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0];
        foreach ($results as $result) {
            $letter = strtoupper($result->letter);
            $data[$letter] = $result->count;
        }
        return response()->json($data);
    }

    /**
     * Zeigt die Auswertungsseite an (nach der letzten Frage).
     */
    public function summary()
    {
        $questions = QuizQuestion::with('options')->orderBy('sort_order')->get();

        foreach ($questions as $question) {
            $question->results = DB::table('scans')
                ->join('qr_codes', 'scans.qr_code_id', '=', 'qr_codes.id')
                ->select('qr_codes.letter', DB::raw('count(scans.id) as count'))
                ->where('scans.quiz_question_id', $question->id)
                ->groupBy('qr_codes.letter')
                ->get();
        }
        return view('quiz.summary', compact('questions'));
    }

    /**
     * Setzt das Quiz zurück, indem alle Scan-Daten gelöscht werden.
     */
    public function reset()
    {
        DB::table('scans')->truncate();
        return redirect()->route('quiz.index');
    }

    /**
     * Aktualisiert den aktiven Fragensatz.
     * Erwartet einen POST-Request mit 'quiz_question_id'.
     */
    public function updateActiveQuestion(Request $request)
    {
        $data = $request->validate([
            'quiz_question_id' => 'required|integer|exists:quiz_questions,id',
        ]);

        DB::table('active_quiz')->update([
            'quiz_question_id' => $data['quiz_question_id'],
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}
