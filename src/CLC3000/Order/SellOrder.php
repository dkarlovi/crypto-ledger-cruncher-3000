<?php

declare(strict_types=1);

/*
 * This file is part of the CLC3000 package.
 *
 * (c) Dalibor Karlović
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dkarlovi\CLC3000\Order;

use Dkarlovi\CLC3000\AssetPair;
use Dkarlovi\CLC3000\Order;

/**
 * Class SellOrder.
 */
class SellOrder implements Order
{
    use OrderTrait;

    /**
     * @param string    $id
     * @param AssetPair $pair
     * @param string    $type
     */
    public function __construct(string $id, AssetPair $pair, string $type)
    {
        $this->id = $id;
        $this->pair = $pair;
        $this->type = $type;
    }
}
