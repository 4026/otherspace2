<?php

namespace OtherSpace2\Models;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * OtherSpace2\Models\User
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\User whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\User whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\OtherSpace2\Models\Item[] $items
 * @property-read \Illuminate\Database\Eloquent\Collection|\OtherSpace2\Models\Marker[] $markers
 */
class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The items that this user owns.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|Builder
     */
    public function items()
    {
        return $this->hasMany('OtherSpace2\Models\Item');
    }

    /**
     * The markers that this user has placed.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|Builder
     */
    public function markers()
    {
        return $this->hasMany('OtherSpace2\Models\Marker', 'creator_id');
    }
}
