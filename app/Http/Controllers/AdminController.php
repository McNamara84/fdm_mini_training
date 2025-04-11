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
                // und es wird eine URL aufgebaut.
                foreach (['A', 'B', 'C', 'D'] as $letter) {
                    $token = Str::random(10);
                    $url = $baseUrl . "?option={$letter}&token={$token}";
                    $set[$letter] = $url;
                }
                $qrSets[] = $set;
            }
        }

        // Übergib die Anzahl sowie die erzeugten QR-Code-Sets an die View
        return view('admin.qrcodes', compact('qrSets', 'n'));
    }
}
