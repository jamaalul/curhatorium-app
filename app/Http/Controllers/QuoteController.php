<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quote;

class QuoteController extends Controller
{
    public function quoteOfTheDay()
    {
        $count = Quote::count();

        if ($count === 0) {
            return response()->json(['message' => 'No quotes available'], 404);
        }

        // Gunakan hari ini sebagai "index" agar berubah setiap hari
        $index = now()->dayOfYear % $count;

        $quote = Quote::skip($index)->take(1)->first();

        return response()->json($quote);
    }
}