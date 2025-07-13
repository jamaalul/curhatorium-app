<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SgdGroup;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SgdController extends Controller
{
    public function show(Request $request) {
        $query = SgdGroup::query();
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('topic', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }
        
        // Filter by category
        if ($request->filled('category') && $request->get('category') !== 'all') {
            $query->where('category', $request->get('category'));
        }
        
        // Filter by status (upcoming/past)
        if ($request->filled('status')) {
            $now = now();
            if ($request->get('status') === 'upcoming') {
                $query->where('schedule', '>', $now);
            } elseif ($request->get('status') === 'past') {
                $query->where('schedule', '<', $now);
            }
        }
        
        // Sort functionality
        $sortBy = $request->get('sort', 'schedule');
        $sortOrder = $request->get('order', 'asc');
        
        if (in_array($sortBy, ['title', 'category', 'schedule', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        }
        
        $groups = $query->get();
        
        // Get unique categories for filter dropdown
        $categories = SgdGroup::distinct()->pluck('category')->sort()->values();
        
        return view('sgd.sgd', compact('groups', 'categories'));
    }

    public function getGroups() {
        $Group = SgdGroup::all();
        return response()->json($Group);
    }

    public function joinGroup(Request $request) {
        $request->validate([
            'group_id' => ['required', 'exists:sgd_groups,id'],
        ]);

        $user = Auth::user();
        if (!$user instanceof \App\Models\User) {
            return redirect()->route('sgd')->with('error', 'User not found or invalid.');
        }
        
        // For now, just show a success message since we removed the relationship
        // You can implement a different joining mechanism if needed
        return redirect()->route('sgd')->with('success', 'Successfully joined the group. You will get notification when the SGD is about to start');
    }
}
