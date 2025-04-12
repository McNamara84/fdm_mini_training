<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QRCode extends Model
{
    use HasFactory;

    protected $table = 'qr_codes';

    protected $fillable = [
        'group_id',
        'letter',
    ];

    /**
     * Beziehung zur zugehörigen Gruppe.
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Beziehung zu den Scans, in denen dieser QR-Code genutzt wurde.
     */
    public function scans()
    {
        return $this->hasMany(Scan::class);
    }
}
