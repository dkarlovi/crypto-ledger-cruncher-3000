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
 * Class SegmentedAmount.
 */
class SegmentedAmount implements Amount
{
    /**
     * @var AmountSegment[]
     */
    private $segments;

    /**
     * @param AmountSegment[] $segments
     */
    public function __construct(array $segments)
    {
        $this->segments = $segments;
    }

    /**
     * @inheritdoc
     */
    public function getTotal(): float
    {
        return array_sum(array_map(function (AmountSegment $segment) {
            return $segment->amount;
        }, $this->segments));
    }

    /**
     * @param Amount $amount
     *
     * @throws \InvalidArgumentException
     */
    public function increment(Amount $amount): void
    {
        if (false === $amount instanceof self) {
            throw new \InvalidArgumentException('Segmented amount can only be altered by a segmented amount');
        }

        $this->segments += $amount->segments;
    }

    /**
     * @inheritdoc
     */
    public function decrement(Amount $amount): void
    {
        if (false === $amount instanceof self) {
            throw new \InvalidArgumentException('Segmented amount can only be altered by a segmented amount');
        }

        die('decrement segmented amount');
    }
}
