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
 * Interface Ledger.
 */
interface Ledger
{
    /**
     * @param array $spec
     *
     * @throws \InvalidArgumentException
     */
    public function addTransactionFromLoaderSpec(array $spec): void;

    /**
     * @return Order[]
     */
    public function getOrders(): array;

    /**
     * @return Asset[]
     */
    public function getAssets(): array;
}
