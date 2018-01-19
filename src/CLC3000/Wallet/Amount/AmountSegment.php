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

/**
 * Class Segment.
 */
class AmountSegment
{
    /**
     * @var float
     */
    public $amount;

    /**
     * @var null|float
     */
    public $cost;

    /**
     * @param float $amount
     * @param float $cost
     */
    public function __construct(float $amount, float $cost = null)
    {
        $this->amount = $amount;
        $this->cost = $cost;
    }
}
