<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SgdGroup;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SgdController extends Controller
{
    public function show() {
        return view('sgd.sgd');
    }

    public function getGroups() {
        $Group = SgdGroup::all();
        return response()->json($Group);
    }

    public function createGroup(Request $request) {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'topic' => ['required', 'string', 'max:255'],
        ]);

        SgdGroup::create([
            'title' => $request->title,
            'topic' => $request->topic,
            'meeting_address' => urlencode(Str::random(12))
        ]);

        return redirect()->route('sgd');
    }

    public function joinGroup(Request $request) {
        $request->validate([
            'group_id' => ['required', 'exists:sgd_groups,id'],
        ]);

        $user = Auth::user(); // Ensure this returns an instance of App\Models\User
        if (!$user instanceof \App\Models\User) {
            return response()->json(['error' => 'User not found or invalid.'], 404);
        }
        $user->group_id = $request->group_id;
        $user->save();

        return response()->json(['message' => 'Successfully joined the group.']);
    }

}
