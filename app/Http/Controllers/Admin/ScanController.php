<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Scan;
use App\Models\QRCode;
use App\Models\QuizQuestion;
use Illuminate\Support\Facades\DB;

/**
 * Controller for handling QR code scans for quiz questions in the admin panel.
 */
class ScanController extends Controller
{
    /**
     * Show the scan page.
     *
     * Reads the currently active quiz question ID from the 'active_quiz' status table.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        // Retrieve the current active quiz record
        $activeQuizRecord = DB::table('active_quiz')->first();

        $activeQuestion = null;
        if ($activeQuizRecord) {
            // Load the active quiz question with its options
            $activeQuestion = QuizQuestion::with('options')->find($activeQuizRecord->quiz_question_id);
        }

        return view('admin.scan.index', compact('activeQuestion'));
    }

    /**
     * Handle AJAX request when a QR code is scanned.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate incoming data
        $data = $request->validate([
            'qr_data' => 'required|string',           // Expected format e.g. "Group: 1, Option: A"
            'quiz_question_id' => 'required|integer|exists:quiz_questions,id',
        ]);

        // Match expected "Group: X, Option: Y" pattern
        $pattern = '/Group:\s*(\d+),\s*Option:\s*([A-D])/i';
        if (preg_match($pattern, $data['qr_data'], $matches)) {
            $groupId = $matches[1];
            $optionLetter = strtoupper($matches[2]);

            // Find corresponding QR code
            $qrCode = QRCode::where('group_id', $groupId)
                ->where('letter', $optionLetter)
                ->first();

            if (!$qrCode) {
                return response()->json(['error' => 'QR code not found.'], 404);
            }

            // Check if a scan already exists for this group and question
            $exists = Scan::where('quiz_question_id', $data['quiz_question_id'])
                ->where('group_id', $groupId)
                ->exists();

            if ($exists) {
                return response()->json(['error' => 'A scan has already been recorded for this group.'], 409);
            }

            // Save the new scan record
            $scan = Scan::create([
                'quiz_question_id' => $data['quiz_question_id'],
                'qr_code_id' => $qrCode->id,
                'group_id' => $groupId,
            ]);

            return response()->json(['success' => true, 'scan' => $scan]);
        }

        // Invalid QR data format
        return response()->json(['error' => 'Invalid QR code format.'], 400);
    }
}
