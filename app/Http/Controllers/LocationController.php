<?php

namespace OtherSpace2\Http\Controllers;

use OtherSpace2\Http\Requests;
use OtherSpace2\Rules\Location;

class LocationController extends Controller
{
    public function getLocation($latitude, $longitude)
    {
        $location = new Location(floatval($latitude), floatval($longitude));

        return response()->json($location);
    }
}
