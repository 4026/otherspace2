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
        $js_environment = [
            'message_grammar'            => Cache::rememberForever(
                'message_grammar',
                function () {
                    return json_decode(file_get_contents(base_path('/resources/assets/json/message_grammar.json')));
                }
            ),
            'item_marker_collect_radius' => config('otherspace.item_marker_collect_radius'),
        ];


        return view('index', compact('js_environment'));
    }
}
