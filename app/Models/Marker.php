<?php

namespace OtherSpace2\Models;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * OtherSpace2\Models\Marker
 *
 * @property integer $id
 * @property integer $location_id
 * @property integer $creator_id
 * @property string $body_text
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Marker whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Marker whereLocationId($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Marker whereCreatorId($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Marker whereBodyText($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Marker whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Marker whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property float $latitude
 * @property float $longitude
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Marker whereLatitude($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Marker whereLongitude($value)
 * @property-read \OtherSpace2\Models\Message $message
 * @property-read \OtherSpace2\Models\User $creator
 */
class Marker extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float'
    ];

    /**
     * The creator of this marker.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Builder
     */
    public function creator()
    {
        return $this->belongsTo('OtherSpace2\Models\User', 'creator_id');
    }

    /**
     * The message associated with this marker (if there is one).
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|Builder
     */
    public function message()
    {
        return $this->hasOne('OtherSpace2\Models\Message');
    }
}
