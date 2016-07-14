<?php

namespace OtherSpace2\Rules;

use Four026\Phable\Grammar;
use Four026\Phable\Node;
use Four026\Phable\Trace;
use OtherSpace2\Models\Location as LocationModel;

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

    public function __construct(LocationModel $model)
    {
        $this->model = $model;

        //Calculate location and time seeds
        $this->location_seed = intval(
            (floor($model->min_longitude / self::getTileWidthAtLatitude($model->min_latitude)) % 10000) * 10000
            + floor($model->min_latitude / self::TILE_HEIGHT_DEG) % 10000
        );
        $this->time_seed     = intval(floor(time() / 3600)) + $this->location_seed;

        //Load grammar
        $this->grammar = new Grammar(base_path(self::GRAMMAR_PATH));
        $this->grammar->addNode('regionName', new Node($this->getLocationName()));
    }

    /**
     * @param float $latitude
     * @param float $longitude
     *
     * @return Location
     */
    public static function getLocationContainingPoint($latitude, $longitude)
    {
        //Fetch model from the DB...
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

        $tile_width                    = self::getTileWidthAtLatitude($latitude);
        $location_model->min_longitude = floor($longitude / $tile_width) * $tile_width;
        $location_model->max_longitude = $location_model->min_longitude + $tile_width;

        //Determine region name
        $location_rules = new Location($location_model);
        $location_model->name = $location_rules->getLocationName();

        $location_model->save();

        return $location_rules;
    }

    /**
     * @return string
     */
    public function getLocationName()
    {
        if (!isset($this->location_name)) {
            $trace = new Trace($this->grammar);
            $trace
                ->setSeed($this->location_seed)
                ->setStartSymbol('regionNameOrigin');

            $this->location_name = $trace->getText();
        }

        return $this->location_name;
    }

    /**
     * @return string
     */
    public function getLocationText()
    {
        $trace = new Trace($this->grammar);
        $trace
            ->setSeed($this->location_seed)
            ->setStartSymbol('locationTextOrigin');

        return $trace->getText();
    }

    /**
     * @return string
     */
    public function getTimeText()
    {
        $trace = new Trace($this->grammar);
        $trace
            ->setSeed($this->time_seed)
            ->setStartSymbol('timeOrigin');

        return $trace->getText();
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
        return [
            'location_bounds' => [
                ['lat' => $this->model->min_latitude, 'long' => $this->model->min_longitude],
                ['lat' => $this->model->max_latitude, 'long' => $this->model->max_longitude],
            ],
            'locationName'    => $this->getLocationName(),
            'locationText'    => explode("\n\n", $this->getLocationText()),
            'timeText'        => explode("\n\n", $this->getTimeText()),
            'messages'        => $this->getMessages()
        ];
    }

    public function getMessages()
    {
        return $this->model->markers;
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
}