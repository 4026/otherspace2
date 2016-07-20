<?php

namespace OtherSpace2\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

/**
 * OtherSpace2\Models\Tag
 *
 * @property integer $id
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection|\OtherSpace2\Models\Noun[] $nouns
 * @property-read \Illuminate\Database\Eloquent\Collection|\OtherSpace2\Models\Adjective[] $adjectives
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Tag whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Tag whereName($value)
 * @mixin \Eloquent
 */
class Tag extends Model
{
    /**
     * The nouns that bestow this tag.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany|Builder
     */
    public function nouns()
    {
        return $this->belongsToMany('OtherSpace2\Models\Noun');
    }

    /**
     * The adjectives that bestow this tag.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany|Builder
     */
    public function adjectives()
    {
        return $this->belongsToMany('OtherSpace2\Models\Adjective');
    }
}
