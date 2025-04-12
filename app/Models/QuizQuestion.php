<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $table = 'quiz_questions';

    // Felder, die mass-assignable sind.
    protected $fillable = [
        'question_text',
        'sort_order',
    ];

    /**
     * Beziehung zu den Antwortmöglichkeiten der Frage.
     */
    public function options()
    {
        return $this->hasMany(QuizOption::class);
    }

    /**
     * Beziehung zu den Scan-Ergebnissen für die Frage.
     */
    public function scans()
    {
        return $this->hasMany(Scan::class);
    }
}
