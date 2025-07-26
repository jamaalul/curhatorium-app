<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quote;
use Illuminate\Support\Facades\DB;

class QuoteController extends Controller
{
    public function quoteOfTheDay()
    {
        $count = Quote::count();

        if ($count === 0) {
            return response()->json(['message' => 'No quotes available'], 404);
        }

        // Use today's date as seed for consistent random selection each day
        $today = now()->format('Y-m-d');
        $dayOfYear = now()->dayOfYear;
        
        // Get all quotes and select 3 based on the day
        $allQuotes = Quote::all();
        
        // Use the day of year to determine which quotes to show
        $quotes = collect();
        for ($i = 0; $i < 3; $i++) {
            $index = ($dayOfYear + $i) % $allQuotes->count();
            $quotes->push($allQuotes[$index]);
        }

        return response()->json($quotes);
    }
}