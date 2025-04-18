<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuizQuestion;
use Illuminate\Support\Facades\DB;

/**
 * Controller for displaying the quiz, fetching results, and managing quiz state.
 */
class QuizController extends Controller
{
    /**
     * Show the quiz homepage with all questions and their options.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $questions = QuizQuestion::with('options')->orderBy('sort_order')->get();
        return view('quiz.index', compact('questions'));
    }

    /**
     * Return current scan counts for a given question as JSON.
     *
     * @param  int  $questionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getResults($questionId)
    {
        // Build query to count scans per QR code option for the question
        $results = DB::table('scans')
            ->join('qr_codes', 'scans.qr_code_id', '=', 'qr_codes.id')
            ->select('qr_codes.letter', DB::raw('count(scans.id) as count'))
            ->where('scans.quiz_question_id', $questionId)
            ->groupBy('qr_codes.letter')
            ->get();

        // Initialize default counts for options A-D
        $data = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0];
        foreach ($results as $result) {
            $letter = strtoupper($result->letter);
            $data[$letter] = $result->count;
        }

        return response()->json($data);
    }

    /**
     * Show the summary page with results for all questions.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function summary()
    {
        $questions = QuizQuestion::with('options')->orderBy('sort_order')->get();

        // Attach scan counts to each question
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
     * Reset the quiz by truncating all scan records.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset()
    {
        DB::table('scans')->truncate();
        return redirect()->route('quiz.index');
    }

    /**
     * Update the active quiz question in the status table.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateActiveQuestion(Request $request)
    {
        $data = $request->validate([
            'quiz_question_id' => 'required|integer|exists:quiz_questions,id',
        ]);

        // Update active question record
        DB::table('active_quiz')->update([
            'quiz_question_id' => $data['quiz_question_id'],
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}
