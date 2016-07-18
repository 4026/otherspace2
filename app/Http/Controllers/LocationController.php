<?php

namespace OtherSpace2\Http\Controllers;


use Auth;
use Illuminate\Http\Request;
use OtherSpace2\Models\Marker;
use OtherSpace2\Models\Message;
use OtherSpace2\Rules\Location;


class LocationController extends Controller
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
     * Get information about the player's current location.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLocation(Request $request)
    {
        $this->validate(
            $request,
            [
                'latitude'  => 'numeric|required|between:-90,90',
                'longitude' => 'numeric|required|between:-180,180'
            ]
        );

        $latitude  = floatval($request->input('latitude'));
        $longitude = floatval($request->input('longitude'));

        $location = Location::getLocationContainingPoint($latitude, $longitude);

        return response()->json(
            [
                'player' => ['lat' => $latitude, 'long' => $longitude],
                'area'   => $location
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addMessage(Request $request)
    {
        $this->validate(
            $request,
            [
                'latitude'                   => 'numeric|required|between:-90,90',
                'longitude'                  => 'numeric|required|between:-180,180',
                'message.clause_1.type'      => 'numeric|required',
                'message.clause_1.word_list' => 'string|required',
                'message.clause_1.word'      => 'numeric|required',
                'message.conjunction'        => 'numeric',
                'message.clause_2.type'      => 'numeric',
                'message.clause_2.word_list' => 'string',
                'message.clause_2.word'      => 'numeric',
            ]
        );

        $user = Auth::user();

        //Determine the grid square that this location falls into.
        $location = Location::getLocationContainingPoint($request->input('latitude'), $request->input('longitude'));

        //Create new marker
        $marker              = new Marker();
        $marker->location_id = $location->getModel()->id;
        $marker->creator_id  = $user->id;
        $marker->latitude    = $request->input('latitude');
        $marker->longitude   = $request->input('longitude');
        $marker->save();

        $message                     = new Message();
        $message->clause_1_id        = $request->input('message.clause_1.type');
        $message->clause_1_word_list = $request->input('message.clause_1.word_list');
        $message->clause_1_word_id   = $request->input('message.clause_1.word');
        $message->conjunction        = $request->input('message.conjunction');
        $message->clause_2_id        = $request->input('message.clause_2.type');
        $message->clause_2_word_list = $request->input('message.clause_2.word_list');
        $message->clause_2_word_id   = $request->input('message.clause_2.word');

        $marker->message()->save($message);

        return response()->json(['success' => true]);
    }
}
