<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Represents a QR code for a specific group and its usage in scans.
 *
 * @property int $id
 * @property int $group_id
 * @property string $letter
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class QRCode extends Model
{
    use HasFactory;

    protected $table = 'qr_codes';

    protected $fillable = [
        'group_id',
        'letter',
    ];

    /**
     * Get the group that owns this QR code.
     *
     * @return BelongsTo<Group, QRCode>
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get all scan records in which this QR code was used.
     *
     * @return HasMany<Scan>
     */
    public function scans(): HasMany
    {
        return $this->hasMany(Scan::class);
    }
}
