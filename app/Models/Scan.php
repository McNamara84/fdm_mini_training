<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Represents a scan of a QR code for a specific quiz question by a group.
 *
 * @property int         $id
 * @property int         $quiz_question_id
 * @property int         $qr_code_id
 * @property int         $group_id
 * @property Carbon|null $scanned_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Scan extends Model
{
    use HasFactory;

    protected $table = 'scans';

    protected $fillable = [
        'quiz_question_id',
        'qr_code_id',
        'group_id',
        'scanned_at',
    ];

    /**
     * Get the quiz question associated with this scan.
     *
     * @return BelongsTo<QuizQuestion, Scan>
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(QuizQuestion::class, 'quiz_question_id');
    }

    /**
     * Get the QR code that was scanned.
     *
     * @return BelongsTo<QRCode, Scan>
     */
    public function qrCode(): BelongsTo
    {
        return $this->belongsTo(QRCode::class, 'qr_code_id');
    }

    /**
     * Get the group that performed the scan.
     *
     * @return BelongsTo<Group, Scan>
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}