<?php
namespace OtherSpace2\Rules;

use OtherSpace2\Models\Item;
use OtherSpace2\Models\User;

/**
 * Class ItemMarker
 *
 * @package OtherSpace2\Rules
 */
class ItemMarker
{
    public  $position;
    private $item_adjective_id;
    private $item_noun_id;

    public function __construct(Position $position, $item_adjective_id, $item_noun_id)
    {
        $this->position = $position;
        $this->item_adjective_id = $item_adjective_id;
        $this->item_noun_id = $item_noun_id;
    }

    /**
     * @param User     $user
     *
     * @return Item
     */
    public function claimFor(User $user)
    {
        //Create a new item of the type handed out by this marker, and add it to the user's inventory.
        $item = new Item(['adjective_id' => $this->item_adjective_id,'noun_id' => $this->item_noun_id]);
        $user->items()->save($item);

        return $item;
    }
}