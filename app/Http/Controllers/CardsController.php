<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CardsController extends Controller
{
    public function index() {
        $cards = \App\Models\Card::inRandomOrder()->limit(5)->get();
        return view('cards', compact('cards'));
    }
}
