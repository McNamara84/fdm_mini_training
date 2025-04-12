<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Group extends Model
{
    use HasFactory;

    protected $table = 'groups';

    protected $fillable = [
        'name',
    ];

    /**
     * Beziehung zu den QR-Codes, die dieser Gruppe zugeordnet sind.
     */
    public function qrCodes()
    {
        return $this->hasMany(QRCode::class);
    }

    /**
     * Beziehung zu den Scan-Ergebnissen dieser Gruppe.
     */
    public function scans()
    {
        return $this->hasMany(Scan::class);
    }
}
