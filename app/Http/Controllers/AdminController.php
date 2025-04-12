<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Zeigt das Dashboard an, in dem die Live-Ergebnisse (Aggregierte Votes)
     * aller Fragen angezeigt werden.
     */
    public function dashboard()
    {
        $questions = Question::with('options.votes')->orderBy('order')->get();

        return view('admin.dashboard', compact('questions'));
    }

    /**
     * Zeigt das Formular zur Erzeugung von QR-Code-Sets und generiert diese.
     */
    public function qrCodes(Request $request)
    {
        // Basis-URL: hier kommt deine URL zur Quizseite
        $baseUrl = 'https://fdm-mini-training-main-e9c4ja.laravel.cloud/quiz';

        // Standardmäßig 0 Sets, wenn keine Eingabe erfolgt
        $n = $request->input('number', 0);
        $qrSets = [];

        if ($n > 0) {
            // Für jede gewünschte Menge an Sets...
            for ($i = 0; $i < $n; $i++) {
                $set = [];
                // Für die Optionen A, B, C und D wird jeweils ein zufälliger Token generiert,
                // der in der vote_tokens-Tabelle gespeichert wird.
                foreach (['A', 'B', 'C', 'D'] as $letter) {
                    $token = Str::random(10);

                    // Speichere den Token in die vote_tokens-Tabelle.
                    \DB::table('vote_tokens')->insert([
                        'question_id' => 1, // hier Beispiel: Frage mit ID 1
                        'option_letter' => $letter,
                        'token' => $token,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Erstelle dann die URL inklusive aller nötigen Parameter.
                    $url = $baseUrl . "?question_id=1&option={$letter}&token={$token}";
                    $set[$letter] = $url;
                }
                $qrSets[] = $set;
            }
        }

        // Übergib die Anzahl sowie die erzeugten QR-Code-Sets an die View
        return view('admin.qrcodes', compact('qrSets', 'n'));
    }


    /**
     * Zeigt die mobile Scan-Seite, auf der QR-Codes erfasst werden können.
     */
    public function scan()
    {
        return view('admin.scan');
    }
}
