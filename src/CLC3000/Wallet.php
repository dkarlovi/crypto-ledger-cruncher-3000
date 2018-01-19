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
 * Interface Wallet.
 */
interface Wallet
{
    /**
     * @return Asset
     */
    public function getAsset(): Asset;

    /**
     * @return float
     */
    public function getTotal(): float;

    /**
     * @param Amount $amount
     *
     * @throws \InvalidArgumentException
     *
     * @return float New total
     */
    public function deposit(Amount $amount): float;

    /**
     * @param Amount $amount
     *
     * @throws \InvalidArgumentException
     *
     * @return float New total
     */
    public function withdraw(Amount $amount): float;
}
