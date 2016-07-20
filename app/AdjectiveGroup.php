<?php

namespace OtherSpace2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class AdjectiveGroup extends Model
{
    /**
     * The adjectives that belong to this group.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|Builder
     */
    public function adjectives()
    {
        return $this->hasMany('OtherSpace2\Models\Adjectives', 'group_id');
    }

    /**
     * The nouns that may be paired with an adjective from this group.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany|Builder
     */
    public function nouns()
    {
        return $this->belongsToMany('OtherSpace2\Models\AdjectiveGroups', 'noun_adjective_group');
    }
}
