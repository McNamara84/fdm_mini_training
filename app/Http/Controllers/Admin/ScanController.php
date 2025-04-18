<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Scan;
use App\Models\QRCode;
use App\Models\QuizQuestion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controller for handling QR code scans for quiz questions in the admin panel.
 */
class ScanController extends Controller
{
    /**
     * Show the scan page.
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
        try {
            // Validate incoming data
            $data = $request->validate([
                'qr_data' => 'required|string',
                'quiz_question_id' => 'required|integer|exists:quiz_questions,id',
            ]);

            // Log received data for debugging
            Log::info('QR Scan received', [
                'qr_data' => $data['qr_data'],
                'quiz_question_id' => $data['quiz_question_id']
            ]);

            // Try different patterns to extract group ID and option letter
            $groupId = null;
            $optionLetter = null;

            // Pattern 1: "Group: X, Option: Y"
            $pattern1 = '/Group:\s*(\d+),\s*Option:\s*([A-D])/i';
            if (preg_match($pattern1, $data['qr_data'], $matches)) {
                $groupId = $matches[1];
                $optionLetter = strtoupper($matches[2]);
            }
            // Pattern 2: Simple "G{number}O{letter}" format
            // e.g. "G1OA" for Group 1, Option A
            else if (preg_match('/G(\d+)O([A-D])/i', $data['qr_data'], $matches)) {
                $groupId = $matches[1];
                $optionLetter = strtoupper($matches[2]);
            }
            // Pattern 3: Try to extract numeric values for direct QR code IDs
            else if (is_numeric($data['qr_data'])) {
                // If QR code contains only a number, try to find the QR code by ID
                $qrCode = QRCode::find($data['qr_data']);
                if ($qrCode) {
                    $groupId = $qrCode->group_id;
                    $optionLetter = $qrCode->letter;
                }
            }

            // If we couldn't extract the information
            if (!$groupId || !$optionLetter) {
                Log::warning('Invalid QR format', ['qr_data' => $data['qr_data']]);
                return response()->json([
                    'success' => false,
                    'error' => 'QR-Code-Format nicht erkannt. Erwartetes Format: "Group: X, Option: Y" oder "GXOY".'
                ], 400);
            }

            // Find corresponding QR code
            $qrCode = QRCode::where('group_id', $groupId)
                ->where('letter', $optionLetter)
                ->first();

            if (!$qrCode) {
                Log::warning('QR code not found in database', [
                    'group_id' => $groupId,
                    'letter' => $optionLetter
                ]);
                return response()->json([
                    'success' => false,
                    'error' => "QR-Code für Gruppe $groupId, Option $optionLetter nicht gefunden."
                ], 404);
            }

            // Check if a scan already exists for this group and question
            $existingScan = Scan::where('quiz_question_id', $data['quiz_question_id'])
                ->where('group_id', $groupId)
                ->first();

            if ($existingScan) {
                // Update the existing scan with new option
                $existingScan->qr_code_id = $qrCode->id;
                $existingScan->save();

                Log::info('Scan updated', ['scan_id' => $existingScan->id]);
                return response()->json([
                    'success' => true,
                    'scan' => $existingScan,
                    'message' => "Antwort für Gruppe $groupId wurde aktualisiert."
                ]);
            }

            // Save the new scan record
            $scan = Scan::create([
                'quiz_question_id' => $data['quiz_question_id'],
                'qr_code_id' => $qrCode->id,
                'group_id' => $groupId,
            ]);

            Log::info('New scan created', ['scan_id' => $scan->id]);
            return response()->json([
                'success' => true,
                'scan' => $scan,
                'message' => "Antwort für Gruppe $groupId wurde gespeichert."
            ]);
        } catch (\Exception $e) {
            Log::error('Exception in ScanController::store', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Serverfehler: ' . $e->getMessage()
            ], 500);
        }
    }
}