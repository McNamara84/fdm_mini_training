<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\QRCode;

/**
 * Controller for managing QR codes in the admin panel.
 *
 * Ensures each group has QR codes for letters A-D.
 */
class QRCodesController extends Controller
{
    /**
     * Display a list of groups with their QR codes.
     *
     * Ensures each group has QR codes for all four options (A, B, C, D).
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        // Load groups with existing QR codes
        $groups = Group::with('qrCodes')->orderBy('id')->get();

        // Ensure QR codes for letters A-D exist for each group
        foreach ($groups as $group) {
            $letters = ['A', 'B', 'C', 'D'];
            foreach ($letters as $letter) {
                if (!$group->qrCodes->contains('letter', $letter)) {
                    // Create missing QR code entry
                    QRCode::create([
                        'group_id' => $group->id,
                        'letter' => $letter,
                    ]);
                }
            }
        }

        // Reload groups after potential changes
        $groups = Group::with('qrCodes')->orderBy('id')->get();

        return view('admin.qrcodes.index', compact('groups'));
    }
}
