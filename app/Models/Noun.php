<?php

namespace OtherSpace2\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

/**
 * OtherSpace2\Models\Noun
 *
 * @property integer $id
 * @property string $word
 * @property-read \Illuminate\Database\Eloquent\Collection|\OtherSpace2\Models\Tag[] $tags
 * @property-read \Illuminate\Database\Eloquent\Collection|\OtherSpace2\Models\Adjective[] $adjectives
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Noun whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Noun whereWord($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\OtherSpace2\Models\AdjectiveGroup[] $adjective_groups
 */
class Noun extends Model
{
    /**
     * The tags bestowed upon any item with this noun.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany|Builder
     */
    public function tags()
    {
        return $this->belongsToMany('OtherSpace2\Models\Tag');
    }

    /**
     * The adjective groups that may be paired with this noun.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany|Builder
     */
    public function adjective_groups()
    {
        return $this->belongsToMany('OtherSpace2\Models\AdjectiveGroup', 'noun_adjective_group');
    }
}
