<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShareAndTalkController extends Controller
{
    public function index() {
        return view('share-and-talk');
    }
}
