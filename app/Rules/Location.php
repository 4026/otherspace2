<?php

namespace OtherSpace2\Rules;

use Four026\Phable\Grammar;
use Four026\Phable\Node;
use Four026\Phable\Trace;
use OtherSpace2\Models\Location as LocationModel;
use OtherSpace2\Models\Marker;

class Location implements \JsonSerializable
{
    const GRAMMAR_PATH = '/resources/assets/json/grammar.json';

    // The difference in degrees between the latitude of the top of the tile and the bottom of the tile.
    const TILE_HEIGHT_DEG = 0.005;

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
    private $location_name;
    /**
     * @var string
     */
    private $location_description;
    /**
     * @var string
     */
    private $time_text;
    /**
     * @var array
     */
    private $item_markers;


    public function __construct(LocationModel $model)
    {
        $this->model = $model;

        //Calculate location and time seeds
        $this->location_seed = intval(
            (floor($model->min_longitude / self::getTileWidthAtLatitude($model->min_latitude)) % 10000) * 10000
            + floor($model->min_latitude / self::TILE_HEIGHT_DEG) % 10000
        );
        $this->time_seed     = intval(floor(time() / 3600)) + $this->location_seed;

        //Load grammar and generate strings.
        $this->grammar = new Grammar(base_path(self::GRAMMAR_PATH));
        $this->generateLocationName();
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
        $location_model->min_latitude = floor($latitude / self::TILE_HEIGHT_DEG) * self::TILE_HEIGHT_DEG;
        $location_model->max_latitude = $location_model->min_latitude + self::TILE_HEIGHT_DEG;

        $tile_width                    = self::getTileWidthAtLatitude($location_model->min_latitude);
        $location_model->min_longitude = floor($longitude / $tile_width) * $tile_width;
        $location_model->max_longitude = $location_model->min_longitude + $tile_width;

        //Determine region name
        $location_rules       = new Location($location_model);
        $location_model->name = $location_rules->getLocationName();

        $location_model->save();

        return $location_rules;
    }

    /**
     * @return string
     */
    public function getLocationName()
    {
        return $this->location_name;
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
                'position' => ['latitude' => $message_marker->latitude, 'longitude' => $message_marker->longitude],
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
            'item_markers'    => $this->item_markers
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
     * @param $latitude
     *
     * @return float
     */
    public static function getTileWidthAtLatitude($latitude)
    {
        return self::TILE_HEIGHT_DEG / cos(deg2rad($latitude));
    }

    private function generateLocationName()
    {
        $trace = new Trace($this->grammar);
        $trace
            ->setSeed($this->location_seed)
            ->setStartSymbol('regionNameOrigin');

        $this->location_name = $trace->getText();
        $this->grammar->addNode('regionName', new Node($this->getLocationName()));
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

        $latitude_multiplier = 10 / self::TILE_HEIGHT_DEG;
        $longitude_multiplier = 10 / self::getTileWidthAtLatitude($this->model->min_latitude);

        $this->item_markers = [];
        for ($i = 0; $i < 5; ++$i) {

            $latitude  = mt_rand(
                $this->model->min_latitude * $latitude_multiplier,
                $this->model->max_latitude * $latitude_multiplier
            ) / $latitude_multiplier;

            $longitude = mt_rand(
                $this->model->min_longitude * $longitude_multiplier,
                $this->model->max_longitude * $longitude_multiplier
            ) / $longitude_multiplier;

            $this->item_markers[] = ['latitude' => $latitude, 'longitude' => $longitude];
        }


    }
}