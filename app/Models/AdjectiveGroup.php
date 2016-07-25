<?php

namespace OtherSpace2\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

/**
 * OtherSpace2\AdjectiveGroup
 *
 * @property integer $id
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection|\OtherSpace2\Models\Adjective[] $adjectives
 * @property-read \Illuminate\Database\Eloquent\Collection|\OtherSpace2\Models\Noun[] $nouns
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\AdjectiveGroup whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\AdjectiveGroup whereName($value)
 * @mixin \Eloquent
 */
class AdjectiveGroup extends Model
{
    /**
     * The adjectives that belong to this group.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|Builder
     */
    public function adjectives()
    {
        return $this->hasMany('OtherSpace2\Models\Adjective', 'group_id');
    }

    /**
     * The nouns that may be paired with an adjective from this group.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany|Builder
     */
    public function nouns()
    {
        return $this->belongsToMany('OtherSpace2\Models\Noun', 'noun_adjective_group');
    }
}
