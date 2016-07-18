<?php

namespace OtherSpace2\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * OtherSpace2\Models\Location
 *
 * @property integer                                                                    $id
 * @property float                                                                      $min_latitude
 * @property float                                                                      $min_longitude
 * @property float                                                                      $max_latitude
 * @property float                                                                      $max_longitude
 * @property string                                                                     $name
 * @property \Carbon\Carbon                                                             $created_at
 * @property \Carbon\Carbon                                                             $updated_at
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Location whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Location whereMinLatitude($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Location whereMinLongitude($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Location whereMaxLatitude($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Location whereMaxLongitude($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Location whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Location whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Location whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\OtherSpace2\Models\Marker[] $markers
 */
class Location extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['min_latitude', 'max_latitude', 'min_longitude', 'max_longitude', 'name'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'min_latitude'  => 'float',
        'max_latitude'  => 'float',
        'min_longitude' => 'float',
        'max_longitude' => 'float'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|Builder
     */
    public function markers()
    {
        return $this->hasMany('OtherSpace2\Models\Marker');
    }
}
