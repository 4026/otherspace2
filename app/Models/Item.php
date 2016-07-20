<?php

namespace OtherSpace2\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

/**
 * OtherSpace2\Models\Item
 *
 * @property integer $id
 * @property integer $owner_id
 * @property integer $adjective_id
 * @property integer $noun_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \OtherSpace2\Models\User $owner
 * @property-read \OtherSpace2\Models\Adjective $adjective
 * @property-read \OtherSpace2\Models\Noun $noun
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Item whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Item whereOwnerId($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Item whereAdjectiveId($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Item whereNounId($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Item whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Item whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Item extends Model
{
    /**
     * The user that owns this item.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Builder
     */
    public function owner()
    {
        return $this->belongsTo('OtherSpace2\Models\User');
    }

    /**
     * The adjective describing this item.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Builder
     */
    public function adjective()
    {
        return $this->belongsTo('OtherSpace2\Models\Adjective');
    }

    /**
     * The noun describing what item this is.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Builder
     */
    public function noun()
    {
        return $this->belongsTo('OtherSpace2\Models\Noun');
    }
}
