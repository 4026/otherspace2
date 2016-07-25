<?php

namespace OtherSpace2\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

/**
 * OtherSpace2\Models\Adjective
 *
 * @property integer $id
 * @property string $word
 * @property-read \Illuminate\Database\Eloquent\Collection|\OtherSpace2\Models\Tag[] $tags
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Adjective whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Adjective whereWord($value)
 * @mixin \Eloquent
 * @property integer $group_id
 * @property-read \OtherSpace2\Models\AdjectiveGroup $groups
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Adjective whereGroupId($value)
 */
class Adjective extends Model
{
    /**
     * The tags that this adjective bestow on an item that it applies to.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany|Builder
     */
    public function tags()
    {
        return $this->belongsToMany('OtherSpace2\Models\Tag');
    }

    /**
     * The group that this adjective belongs to.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany|Builder
     */
    public function groups()
    {
        return $this->belongsTo('OtherSpace2\Models\AdjectiveGroup', 'group_id');
    }
}
