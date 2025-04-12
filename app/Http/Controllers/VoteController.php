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
        $validator = \Validator::make($request->all(), [
            'question_id' => 'required|integer|exists:questions,id',
            'token' => 'required|string',
            'group_identifier' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Ungültige Daten'], 400);
        }

        // Suche in der vote_tokens Tabelle statt in question_options
        $tokenRecord = \DB::table('vote_tokens')
            ->where('question_id', $request->input('question_id'))
            ->where('token', $request->input('token'))
            ->where('used', false)
            ->first();

        if (!$tokenRecord) {
            return response()->json(['error' => 'Ungültiger oder bereits verwendeter QR-Code-Token'], 400);
        }

        // Speichere den Vote, hier wird angenommen, dass question_option_id nicht benötigt wird,
        // da wir über den Token eindeutig die Option ermitteln können.
        Vote::create([
            'question_id' => $tokenRecord->question_id,
            // optional: Du kannst hier auch den Button bzw. Buchstaben speichern:
            // 'question_option_id' => ?  (Falls du diesen Bezug herstellen möchtest)
            'group_identifier' => $request->input('group_identifier'),
        ]);

        // Markiere den Token als verwendet
        \DB::table('vote_tokens')->where('id', $tokenRecord->id)->update(['used' => true]);

        return response()->json(['success' => true]);
    }

}
