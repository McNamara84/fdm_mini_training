<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Represents a quiz question and its associated options and scan results.
 *
 * @property int         $id
 * @property string      $question_text
 * @property int         $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class QuizQuestion extends Model
{
    use HasFactory;

    protected $table = 'quiz_questions';

    // Fields that are mass assignable
    protected $fillable = [
        'question_text',
        'sort_order',
    ];

    /**
     * Get the answer options for this question.
     *
     * @return HasMany<QuizOption>
     */
    public function options(): HasMany
    {
        return $this->hasMany(QuizOption::class);
    }

    /**
     * Get the scan records associated with this question.
     *
     * @return HasMany<Scan>
     */
    public function scans(): HasMany
    {
        return $this->hasMany(Scan::class);
    }
}
