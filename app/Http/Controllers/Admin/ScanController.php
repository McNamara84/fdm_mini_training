<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Scan;
use App\Models\QRCode;
use App\Models\QuizQuestion;

class ScanController extends Controller
{
    /**
     * Zeige die Scan-Seite.
     * Optional: Eine Auswahl der aktuell aktiven Quizfragen zur Zuordnung.
     */
    public function index()
    {
        // Alle Quizfragen laden (sortiert nach der gewünschten Reihenfolge)
        $quizQuestions = QuizQuestion::orderBy('sort_order')->get();

        return view('admin.scan.index', compact('quizQuestions'));
    }

    /**
     * Verarbeite den AJAX-Request, wenn ein QR-Code eingelesen wurde.
     */
    public function store(Request $request)
    {
        // Validierung der Eingabedaten
        $data = $request->validate([
            'qr_data' => 'required|string',           // Erwartetes Format z. B. "Group: 1, Option: A"
            'quiz_question_id' => 'required|integer|exists:quiz_questions,id',
        ]);

        // Das erwartete Format: "Group: X, Option: Y"
        $pattern = '/Group:\s*(\d+),\s*Option:\s*([A-D])/i';
        if (preg_match($pattern, $data['qr_data'], $matches)) {
            $groupId = $matches[1];
            $optionLetter = strtoupper($matches[2]);

            // Suche den passenden QR-Code in der Datenbank
            $qrCode = QRCode::where('group_id', $groupId)
                ->where('letter', $optionLetter)
                ->first();

            if (!$qrCode) {
                return response()->json(['error' => 'QR-Code nicht gefunden.'], 404);
            }

            // Prüfe, ob für diese Gruppe für die ausgewählte Frage bereits eine Antwort erfasst wurde
            $exists = Scan::where('quiz_question_id', $data['quiz_question_id'])
                ->where('group_id', $groupId)
                ->exists();

            if ($exists) {
                return response()->json(['error' => 'Für diese Gruppe wurde bereits ein Scan erfasst.'], 409);
            }

            // Speichere den Scan in der Datenbank
            $scan = Scan::create([
                'quiz_question_id' => $data['quiz_question_id'],
                'qr_code_id' => $qrCode->id,
                'group_id' => $groupId,
            ]);

            return response()->json(['success' => true, 'scan' => $scan]);
        } else {
            return response()->json(['error' => 'Ungültiges QR-Code Format.'], 400);
        }
    }
}
