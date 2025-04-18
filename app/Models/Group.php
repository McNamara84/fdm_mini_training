<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\QRCode;
use App\Models\Scan;

/**
 * Represents a group that owns multiple QR codes and related scan records.
 *
 * @property int $id
 * @property string $name
 */
class Group extends Model
{
    use HasFactory;

    protected $table = 'groups';

    protected $fillable = [
        'name',
    ];

    /**
     * Get the QR codes assigned to this group.
     *
     * @return HasMany<QRCode>
     */
    public function qrCodes(): HasMany
    {
        return $this->hasMany(QRCode::class);
    }

    /**
     * Get all scan records associated with this group.
     *
     * @return HasMany<Scan>
     */
    public function scans(): HasMany
    {
        return $this->hasMany(Scan::class);
    }
}
