<?php

namespace OtherSpace2\Http\Controllers;

use Cache;
use OtherSpace2\Models\Adjective;
use OtherSpace2\Models\Noun;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $nouns      = Cache::rememberForever('nouns', function () { return Noun::pluck('word', 'id'); });
        $adjectives = Cache::rememberForever('adjectives', function () { return Adjective::pluck('word', 'id'); });

        return view('index', compact('nouns', 'adjectives'));
    }
}
