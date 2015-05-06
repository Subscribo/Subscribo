<?php

namespace Subscribo\Omnipay\Shared;

use Omnipay\Common\ItemBag as Base;
use Omnipay\Common\ItemInterface;
use Subscribo\Omnipay\Shared\Item;

/**
 * Class ItemBag
 *
 * @package Subscribo\OmnipaySubscriboShared
 */
class ItemBag extends Base
{
    public function add($item)
    {
        if ($item instanceof Item) {
            $this->items[] = $item;
        } elseif ($item instanceof ItemInterface) {
            $parameters = [
                'name' => $item->getName(),
                'description' => $item->getDescription(),
                'price' => $item->getPrice(),
                'quantity' => $item->getQuantity(),
            ];
            $this->items[] = new Item($parameters);
        } else {
            $this->items[] = new Item($item);
        }
    }
}
