<?php

namespace OtherSpace2\Rules;

/**
 * Class representing a latitude/longitude position on the Earth.
 *
 * @package OtherSpace2\Rules
 */
class Position implements \JsonSerializable
{
    const EARTH_DIAMETER = 12742; //Kilometers. On average. Roughly.

    public $latitude;
    public $longitude;

    public function __construct($latitude, $longitude)
    {
        $this->latitude  = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *        which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return ['latitude' => $this->latitude, 'longitude' => $this->longitude];
    }

    /**
     * Get the distance between this positions and another.
     *
     * @param Position $position
     *
     * @return float
     */
    public function distanceTo(Position $position)
    {
        return self::distanceBetween($this, $position);
    }

    /**
     * Get the distance between two positions, using the Haversine Formula.
     * https://en.wikipedia.org/wiki/Haversine_formula
     *
     * @param Position $a
     * @param Position $b
     *
     * @return float
     */
    public static function distanceBetween(Position $a, Position $b)
    {
        $delta_latitude  = deg2rad($b->latitude - $a->latitude);
        $delta_longitude = deg2rad($b->longitude - $a->longitude);

        $h = self::haversine($delta_latitude) + (
                cos(deg2rad($a->latitude))
                * cos(deg2rad($b->latitude))
                * self::haversine($delta_longitude)
            );

        return self::EARTH_DIAMETER * asin(sqrt($h));
    }

    /**
     * Get the approximate distance between two positions, by assuming that the Earth is flat. Obviously, this works
     * quite a lot better for small distances than for large ones.
     *
     * In theory, this is slightly faster than the Haversine method used for distanceBetween(), but in practice the
     * performance gains seem to be pretty minimal.
     *
     * @param Position $a
     * @param Position $b
     *
     * @return float
     */
    public static function approxDistanceBetween(Position $a, Position $b)
    {
        return sqrt(self::approxDistanceSquaredBetween($a, $b));
    }

    /**
     * Get the approximate distance squared between two positions, by assuming that the Earth is flat. Obviously, this
     * works quite a lot better for small distances than for large ones. Useful for quickly comparing distances, when
     * the precise value of the distance isn't important.
     *
     * @param Position $a
     * @param Position $b
     *
     * @return number
     */
    public static function approxDistanceSquaredBetween(Position $a, Position $b)
    {
        $delta_latitude  = deg2rad($b->latitude - $a->latitude);
        $delta_longitude = deg2rad($b->longitude - $a->longitude);
        $mean_latitude   = deg2rad(($a->latitude + $b->latitude) / 2);

        return pow($delta_latitude, 2) + pow($delta_longitude * cos($mean_latitude), 2);
    }

    /**
     * Calculates the haversine of an angle.
     *
     * @param float $theta An angle expressed in radians.
     *
     * @return float
     */
    private static function haversine($theta)
    {
        $sqrt_haversine = sin($theta / 2);

        return $sqrt_haversine * $sqrt_haversine;
    }
}