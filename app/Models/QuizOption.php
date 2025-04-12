<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuizOption extends Model
{
    use HasFactory;

    protected $table = 'quiz_options';

    protected $fillable = [
        'quiz_question_id',
        'letter',
        'option_text',
        'is_correct',
    ];

    /**
     * Beziehung zu der zugehörigen Frage.
     */
    public function question()
    {
        return $this->belongsTo(QuizQuestion::class, 'quiz_question_id');
    }
}
