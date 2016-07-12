<?php

namespace OtherSpace2\Rules;

use Four026\Phable\Grammar;
use Four026\Phable\Node;
use Four026\Phable\Trace;

class Location implements \JsonSerializable
{
    const GRAMMAR_PATH = '/resources/assets/json/grammar.json';

    // The difference in degrees between the latitude of the top of the tile and the bottom of the tile.
    const TILE_HEIGHT_DEG = 0.005;
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
     * @var array
     */
    private $location_bounds;
    /**
     * @var array
     */
    private $location;

    public function __construct($latitude, $longitude)
    {
        $this->location = ['lat' => $latitude, 'long' => $longitude];

        //Calculate region bounds
        $tile_width              = self::TILE_HEIGHT_DEG / cos(deg2rad($latitude));
        $this->location_bounds   = [];
        $this->location_bounds[] = [
            'lat'  => floor($latitude / self::TILE_HEIGHT_DEG) * self::TILE_HEIGHT_DEG,
            'long' => floor($longitude / $tile_width) * $tile_width
        ];
        $this->location_bounds[] = [
            'lat'  => $this->location_bounds[0]['lat'] + self::TILE_HEIGHT_DEG,
            'long' => $this->location_bounds[0]['long'] + $tile_width
        ];

        //Calculate location and time seeds
        $this->location_seed = intval(
            (floor($longitude / $tile_width) % 10000) * 10000
            + floor($latitude / self::TILE_HEIGHT_DEG) % 10000
        );
        $this->time_seed     = intval(floor(time() / 3600)) + $this->location_seed;

        //Load grammar
        $this->grammar = new Grammar(base_path(self::GRAMMAR_PATH));
        $this->grammar->addNode('regionName', new Node($this->getLocationName()));
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
            'location'        => $this->location,
            'location_bounds' => $this->location_bounds,
            'locationName'    => $this->getLocationName(),
            'locationText'    => explode("\n\n", $this->getLocationText()),
            'timeText'        => explode("\n\n", $this->getTimeText())
        ];
    }
}