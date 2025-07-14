<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Professional;

class ShareAndTalkController extends Controller
{
    public function index() {
        return view('share-and-talk');
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
}
