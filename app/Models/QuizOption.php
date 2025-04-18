<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Represents an answer option for a quiz question.
 *
 * @property int         $id
 * @property int         $quiz_question_id
 * @property string      $letter
 * @property string|null $option_text
 * @property bool        $is_correct
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
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
     * The quiz question this option belongs to.
     *
     * @return BelongsTo<QuizQuestion, QuizOption>
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(QuizQuestion::class, 'quiz_question_id');
    }
}
