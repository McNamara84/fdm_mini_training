<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\QRCode;

class QRCodesController extends Controller
{
    public function index()
    {
        // Lade alle Gruppen mitsamt vorhandener QR-Codes
        $groups = Group::with('qrCodes')->orderBy('id')->get();

        // Für jede Gruppe sicherstellen, dass alle vier Optionen (A, B, C, D) existieren
        foreach ($groups as $group) {
            $letters = ['A', 'B', 'C', 'D'];
            foreach ($letters as $letter) {
                if (!$group->qrCodes->contains('letter', $letter)) {
                    QRCode::create([
                        'group_id' => $group->id,
                        'letter'   => $letter,
                    ]);
                }
            }
        }

        // Erneut die Gruppen laden, um alle QR-Codes aktuell zu haben
        $groups = Group::with('qrCodes')->orderBy('id')->get();

        // Übergabe der Daten an die View
        return view('admin.qrcodes.index', compact('groups'));
    }
}
