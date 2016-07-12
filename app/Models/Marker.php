<?php

namespace OtherSpace2\Models;

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
 */
class Marker extends Model
{
    //
}
