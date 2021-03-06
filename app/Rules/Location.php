<?php

namespace OtherSpace2\Rules;

use Auth;
use Cache;
use Carbon\Carbon;
use Four026\Phable\Grammar;
use Four026\Phable\Node;
use Four026\Phable\Trace;
use OtherSpace2\Models\Location as LocationModel;
use OtherSpace2\Models\Marker;
use OtherSpace2\Models\Noun;
use OtherSpace2\Models\User;

class Location implements \JsonSerializable
{
    const GRAMMAR_PATH = '/resources/assets/json/grammar.json';

    /**
     * @var \OtherSpace2\Models\Location
     */
    private $model;

    /**
     * @var Grammar
     */
    private $grammar;
    /**
     * @var int
     */
    private $location_seed;
    /**
     * @var int
     */
    private $time_seed;
    /**
     * @var string
     */
    private $location_description;
    /**
     * @var string
     */
    private $time_text;
    /**
     * @var ItemMarker[]
     */
    private $item_markers;


    public function __construct(LocationModel $model)
    {
        $this->model = $model;

        //Calculate location and time seeds
        $this->location_seed = intval(
            (floor($model->min_longitude / self::getTileWidthAtLatitude($model->min_latitude)) % 10000) * 10000
            + floor($model->min_latitude / config('otherspace.tile_height_deg')) % 10000
        );
        $this->time_seed     = intval(floor(time() / 3600)) + $this->location_seed;

        //Load grammar and generate strings.
        $this->grammar = new Grammar(base_path(self::GRAMMAR_PATH));

        if (empty($model->name)) {
            $this->generateLocationName();
        }
        $this->grammar->addNode('regionName', new Node($this->getLocationName()));

        $this->generateLocationDescription();
        $this->generateTimeText();

        $this->generateItemMarkers();
    }

    /**
     * @param float $latitude
     * @param float $longitude
     *
     * @return Location
     */
    public static function getLocationContainingPoint($latitude, $longitude)
    {
        /**
         * Fetch model from the DB...
         *
         * @var LocationModel $location_model
         */
        $location_model = LocationModel::query()
            ->where('min_latitude', '<=', $latitude)
            ->where('max_latitude', '>', $latitude)
            ->where('min_longitude', '<=', $longitude)
            ->where('max_longitude', '>', $longitude)
            ->first();

        if ($location_model != null) {
            return new Location($location_model);
        }

        //...or create it if it doesn't exist yet.
        $location_model = new LocationModel();

        //Calculate region bounds
        $tile_height_deg              = config('otherspace.tile_height_deg');
        $location_model->min_latitude = floor($latitude / $tile_height_deg) * $tile_height_deg;
        $location_model->max_latitude = $location_model->min_latitude + $tile_height_deg;

        $tile_width                    = self::getTileWidthAtLatitude($location_model->min_latitude);
        $location_model->min_longitude = floor($longitude / $tile_width) * $tile_width;
        $location_model->max_longitude = $location_model->min_longitude + $tile_width;

        //Generate region data and save model
        $location_rules = new Location($location_model);
        $location_model->save();

        return $location_rules;
    }

    /**
     * @return string
     */
    public function getLocationName()
    {
        return $this->model->name;
    }

    /**
     * @return string
     */
    public function getLocationDescription()
    {
        return $this->location_description;
    }

    /**
     * @return string
     */
    public function getTimeText()
    {
        return $this->time_text;
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *       which is a value of any type other than a resource.
     */
    function jsonSerialize()
    {
        $message_markers    = $this->getMessages();
        $formatted_messages = [];
        foreach ($message_markers as $message_marker) {
            $formatted_messages[] = [
                'position' => new Position($message_marker->latitude, $message_marker->longitude),
                'author'   => $message_marker->creator->name,
                'message'  => [
                    'clause_1'    => [
                        'type'      => $message_marker->message->clause_1_id,
                        'word_list' => $message_marker->message->clause_1_word_list,
                        'word'      => $message_marker->message->clause_1_word_id,
                    ],
                    'conjunction' => $message_marker->message->conjunction,
                    'clause_2'    => [
                        'type'      => $message_marker->message->clause_2_id,
                        'word_list' => $message_marker->message->clause_2_word_list,
                        'word'      => $message_marker->message->clause_2_word_id,
                    ]
                ]
            ];
        }

        return [
            'location_bounds' => [
                ['lat' => $this->model->min_latitude, 'long' => $this->model->min_longitude],
                ['lat' => $this->model->max_latitude, 'long' => $this->model->max_longitude],
            ],
            'locationName'    => $this->getLocationName(),
            'locationText'    => explode("\n\n", $this->getLocationDescription()),
            'timeText'        => explode("\n\n", $this->getTimeText()),
            'messages'        => $formatted_messages,
            'item_markers'    => Auth::check() ? $this->getUnclaimedItemMarkersFor(Auth::user()) : $this->item_markers
        ];
    }

    /**
     * @return Marker[]
     */
    public function getMessages()
    {
        return $this->model->markers()->has('message')->with('message')->get();
    }

    /**
     * @return LocationModel
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param User $user
     *
     * @return ItemMarker[]
     */
    public function getUnclaimedItemMarkersFor(User $user)
    {
        return array_filter(
            $this->item_markers,
            function ($marker_id) use ($user) { return !$this->userHasClaimedItemMarker($user, $marker_id); },
            ARRAY_FILTER_USE_KEY
        );
    }


    /**
     * @param $latitude
     *
     * @return float
     */
    public static function getTileWidthAtLatitude($latitude)
    {
        return config('otherspace.tile_height_deg') / cos(deg2rad($latitude));
    }

    /**
     * Claim an item from the specified marker in this region for the specified user at the specified position,
     * checking that they are permitted to.
     *
     * @param User     $user
     * @param Position $user_position
     * @param int      $marker_id
     *
     * @return \OtherSpace2\Models\Item
     */
    public function claimItemMarker(User $user, Position $user_position, $marker_id)
    {
        if (!array_key_exists($marker_id, $this->item_markers)) {
            throw new \InvalidArgumentException("Invalid marker ID $marker_id");
        }

        //Check that this user has not already collected an item from this marker.
        if ($this->userHasClaimedItemMarker($user, $marker_id)) {
            abort(422, "Item already picked up...");
        }

        $marker = $this->item_markers[$marker_id];

        //Check that the user is close enough to the marker to claim it.
        $marker_distance = $user_position->distanceTo($marker->position);
        $max_distance    = config('otherspace.item_marker_collect_radius');
        if ($marker_distance > $max_distance) {
            abort(
                422,
                "You are $marker_distance km away from the item marker, the maximum distance is $max_distance km."
            );
        }

        // Give the user the item from the marker.
        $item = $marker->claimFor($user);

        // Log that the user has collected an item from this marker, but expire the key from the cache at the end of
        // this hour.
        $expires_at = Carbon::now()->addHour(1)->minute(0)->second(0);
        Cache::put($this->getCacheKeyForMarker($user, $marker_id), true, $expires_at);

        return $item;
    }

    public function userHasClaimedItemMarker(User $user, $marker_id)
    {
        $cache_key = $this->getCacheKeyForMarker($user, $marker_id);

        return boolval(Cache::get($cache_key));
    }

    private function generateLocationName()
    {
        $trace = new Trace($this->grammar);
        $trace
            ->setSeed($this->location_seed)
            ->setStartSymbol('regionNameOrigin');

        $this->model->name = $trace->getText();
    }

    private function generateLocationDescription()
    {
        $trace = new Trace($this->grammar);
        $trace
            ->setSeed($this->location_seed)
            ->setStartSymbol('locationTextOrigin');

        $this->location_description = $trace->getText();
    }

    private function generateTimeText()
    {
        $trace = new Trace($this->grammar);
        $trace
            ->setSeed($this->time_seed)
            ->setStartSymbol('timeOrigin');

        $this->time_text = $trace->getText();
    }

    private function generateItemMarkers()
    {
        //Set random seed...
        mt_srand($this->time_seed);

        // mt_rand only generates ints, so choose multipliers for latitude and longitude so that there are 100 possible
        // locations in the area that an item might appear.
        $grid_resolution     = config('otherspace.tile_grid_resolution');
        $latitude_increment  = ($this->model->max_latitude - $this->model->min_latitude) / $grid_resolution;
        $longitude_increment = ($this->model->max_longitude - $this->model->min_longitude) / $grid_resolution;

        $noun_ids = Cache::rememberForever('noun_ids', function () { return Noun::pluck('id'); });

        $num_markers        = config('otherspace.item_markers_per_tile');
        $this->item_markers = [];
        for ($i = 0; $i < $num_markers; ++$i) {
            $latitude  = $this->model->min_latitude + (mt_rand(0, $grid_resolution) * $latitude_increment);
            $longitude = $this->model->min_longitude + (mt_rand(0, $grid_resolution) * $longitude_increment);

            $position = new Position($latitude, $longitude);

            //Choose a random noun.
            $noun_id = $noun_ids[mt_rand(0, count($noun_ids) - 1)];
            $noun    = Noun::with('adjective_groups.adjectives')->find($noun_id);

            //Build a list of permitted adjective IDs for that noun.
            $permitted_adjectives = [];
            foreach ($noun->adjective_groups as $permitted_adjective_group) {
                foreach ($permitted_adjective_group->adjectives as $adjective) {
                    $permitted_adjectives[] = $adjective->id;
                }
            }
            $permitted_adjectives = array_unique($permitted_adjectives);

            //Choose a random adjective.
            $adjective_id = $permitted_adjectives[mt_rand(0, count($permitted_adjectives) - 1)];

            $this->item_markers[] = new ItemMarker($position, $adjective_id, $noun_id);
        }
    }

    /**
     * @param User $user
     * @param      $marker_id
     *
     * @return string
     */
    private function getCacheKeyForMarker(User $user, $marker_id)
    {
        $cache_key = "tile_{$this->model->id}.marker_$marker_id.user_{$user->id}.claimed";

        return $cache_key;
    }
}