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

namespace Dkarlovi\CLC3000\Wallet\Amount;

use Dkarlovi\CLC3000\Amount;

/**
 * Class BasicAmount.
 */
class BasicAmount implements Amount
{
    /**
     * @var float
     */
    private $total;

    /**
     * @param float $total
     */
    public function __construct(float $total)
    {
        $this->total = $total;
    }

    /**
     * @inheritdoc
     */
    public function getTotal(): float
    {
        return $this->total;
    }

    /**
     * @inheritdoc
     */
    public function increment(Amount $amount): void
    {
        $this->total += $amount->getTotal();
    }

    /**
     * @inheritdoc
     */
    public function decrement(Amount $amount): void
    {
        if ($this->total < $amount->getTotal()) {
            throw new \InvalidArgumentException('Insufficient funds');
        }

        $this->total -= $amount->getTotal();
    }
}
