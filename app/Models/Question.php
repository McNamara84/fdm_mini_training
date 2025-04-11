<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\QuestionOption;
use App\Models\Vote;

class Question extends Model
{
    use HasFactory;

    /**
     * Die Felder, die per Mass Assignment gefüllt werden dürfen.
     */
    protected $fillable = [
        'title',
        'question_text',
        'type',
        'order',
    ];

    /**
     * Beziehung: Eine Frage hat viele Antwortoptionen.
     */
    public function options()
    {
        return $this->hasMany(QuestionOption::class);
    }

    /**
     * Beziehung: Eine Frage hat viele Votes.
     */
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
