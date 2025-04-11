<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Question;
use App\Models\Vote;

class QuestionOption extends Model
{
    use HasFactory;

    /**
     * Die Felder, die per Mass Assignment gefüllt werden dürfen.
     */
    protected $fillable = [
        'question_id',
        'option_letter',
        'option_text',
        'token',
    ];

    /**
     * Beziehung: Eine Antwortoption gehört zu einer Frage.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Beziehung: Eine Antwortoption kann viele Votes haben.
     */
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
