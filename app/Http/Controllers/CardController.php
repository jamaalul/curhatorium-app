<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;

class CardController extends Controller
{
public function index()
{
    // Get 5 random cards from the database
    $pernyataanCards = Card::where('category', 'pernyataan')->inRandomOrder()->limit(2)->get();
    $pertanyaanCards = Card::where('category', 'pertanyaan')->inRandomOrder()->limit(3)->get();
    $cards = $pernyataanCards->concat($pertanyaanCards);

    return view('cards', compact('cards'));
}
}
