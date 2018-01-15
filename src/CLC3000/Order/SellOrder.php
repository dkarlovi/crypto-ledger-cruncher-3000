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

use Dkarlovi\CLC3000\Order;

/**
 * Class SellOrder.
 */
class SellOrder implements Order
{
    /**
     * @var string
     */
    private $id;

    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @param array $spec
     */
    public function addTransactionFromLoaderSpec(array $spec): void
    {
        echo 'sell';
    }
}
