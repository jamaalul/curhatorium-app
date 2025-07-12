<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CardsController extends Controller
{
    public function index() {
        return view('cards', compact('stats'));
    }
}
