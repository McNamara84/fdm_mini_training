<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
     * Beziehung zur Frage, zu der der Scan gehört.
     */
    public function question()
    {
        return $this->belongsTo(QuizQuestion::class, 'quiz_question_id');
    }

    /**
     * Beziehung zum gescannten QR-Code.
     */
    public function qrCode()
    {
        return $this->belongsTo(QRCode::class, 'qr_code_id');
    }

    /**
     * Beziehung zur Gruppe, die den QR-Code eingescannt hat.
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
