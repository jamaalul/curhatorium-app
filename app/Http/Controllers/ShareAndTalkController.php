<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Professional;
use Illuminate\Support\Facades\Auth;

class ShareAndTalkController extends Controller
{
    public function index() {
        return view('share-and-talk.index');
    }

    public function getProfessionals(Request $request)
    {
        $type = $request->query('type');
        $query = Professional::query();
        if ($type) {
            $query->where('type', $type);
        }
        return response()->json($query->get());
    }

    public function chatConsultation($professionalId) {
        $professional = Professional::findOrFail($professionalId);
        $user = Auth::user();

        return view('share-and-talk.chat', ['professional' => $professional, 'user' => $user]);
    }
}
