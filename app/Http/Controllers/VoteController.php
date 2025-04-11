<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Vote;
use App\Models\QuestionOption;

class VoteController extends Controller
{
    /**
     * Speichert eine übermittelte Stimme (Vote).
     * Erwartete Request-Parameter:
     * - question_id: ID der Frage (integer)
     * - token: Eindeutiger Token der Antwortoption (string)
     * - group_identifier: (optional) Bezeichner der Gruppe, die abstimmt (string)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question_id'      => 'required|integer|exists:questions,id',
            'token'            => 'required|string',
            'group_identifier' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Ungültige Daten'], 400);
        }

        // Finde die Antwortoption, die zur Frage und zum übermittelten Token passt.
        $option = QuestionOption::where('question_id', $request->input('question_id'))
                    ->where('token', $request->input('token'))
                    ->first();

        if (!$option) {
            return response()->json(['error' => 'Ungültiger QR-Code-Token'], 400);
        }

        // Speichere die Stimme in der Datenbank.
        Vote::create([
            'question_id'         => $option->question_id,
            'question_option_id'  => $option->id,
            'group_identifier'    => $request->input('group_identifier'),
        ]);

        return response()->json(['success' => true]);
    }
}
