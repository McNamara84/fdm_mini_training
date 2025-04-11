<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;

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
}
