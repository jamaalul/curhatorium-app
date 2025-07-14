<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;

class CardController extends Controller
{
public function index()
{
    // Get 5 random cards from the database
    $cards = Card::inRandomOrder()->limit(5)->get();

    return view('cards', compact('cards'));
}
}
