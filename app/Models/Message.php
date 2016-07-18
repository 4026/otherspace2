<?php

namespace OtherSpace2\Models;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * OtherSpace2\Models\Message
 *
 * @property integer $id
 * @property integer $marker_id
 * @property integer $clause_1_id
 * @property string $clause_1_word_list
 * @property integer $clause_1_word_id
 * @property integer $conjunction
 * @property integer $clause_2_id
 * @property string $clause_2_word_list
 * @property integer $clause_2_word_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \OtherSpace2\Models\Marker $marker
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Message whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Message whereMarkerId($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Message whereClause1Id($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Message whereClause1WordList($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Message whereClause1WordId($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Message whereConjunction($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Message whereClause2Id($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Message whereClause2WordList($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Message whereClause2WordId($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\OtherSpace2\Models\Message whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Message extends Model
{
    /**
     * The marker associated with this message.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Builder
     */
    public function marker()
    {
        return $this->belongsTo('OtherSpace2\Models\Marker');
    }
}
