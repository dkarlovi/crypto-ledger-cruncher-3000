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

namespace Dkarlovi\CLC3000;

/**
 * Interface Amount.
 */
interface Amount
{
    /**
     * @return float
     */
    public function getTotal(): float;

    /**
     * @param Amount $amount
     *
     * @throws \InvalidArgumentException
     */
    public function increment(self $amount): void;

    /**
     * @param Amount $amount
     *
     * @throws \InvalidArgumentException
     */
    public function decrement(self $amount): void;
}
