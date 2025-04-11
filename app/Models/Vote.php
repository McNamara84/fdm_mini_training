<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\QuestionOption;

class Vote extends Model
{
    use HasFactory;

    /**
     * Die Felder, die per Mass Assignment gefüllt werden dürfen.
     */
    protected $fillable = [
        'question_id',
        'question_option_id',
        'group_identifier',
    ];

    /**
     * Beziehung: Ein Vote gehört zu einer Frage.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Beziehung: Ein Vote gehört zu einer Antwortoption.
     */
    public function questionOption()
    {
        return $this->belongsTo(QuestionOption::class);
    }
}
