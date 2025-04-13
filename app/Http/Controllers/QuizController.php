<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuizQuestion;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    /**
     * Zeigt das Quiz an – Startseite der Quiz-Anzeige (/quiz).
     * Alle Fragen werden (sortiert) geladen und an die View übergeben.
     */
    public function index()
    {
        $questions = QuizQuestion::with('options')->orderBy('sort_order')->get();
        return view('quiz.index', compact('questions'));
    }

    /**
     * Liefert per JSON die aktuellen Ergebnisse (Scan-Zählungen) für die übergebene Frage.
     * Wird durch die Live-Aktualisierung (AJAX-Polling) im Frontend abgefragt.
     *
     * @param int $questionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getResults($questionId)
    {
        // Aggregiere die Anzahl der Scans je Antwortoption (Buchstabe)
        $results = DB::table('scans')
                    ->join('qr_codes', 'scans.qr_code_id', '=', 'qr_codes.id')
                    ->select('qr_codes.letter', DB::raw('count(scans.id) as count'))
                    ->where('scans.quiz_question_id', $questionId)
                    ->groupBy('qr_codes.letter')
                    ->get();

        // Stelle sicher, dass für alle Optionen A-D ein Wert (ggf. 0) geliefert wird
        $data = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0];
        foreach ($results as $result) {
            $letter = strtoupper($result->letter);
            $data[$letter] = $result->count;
        }
        return response()->json($data);
    }

    /**
     * Zeigt die Auswertungsseite an (nach der letzten Frage).
     * Es werden alle Fragen samt Antwortoptionen und den ermittelten Scan-Zahlen angezeigt.
     */
    public function summary()
    {
        $questions = QuizQuestion::with('options')->orderBy('sort_order')->get();

        // Für jede Frage die aggregierten Ergebnisse ermitteln
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
}
