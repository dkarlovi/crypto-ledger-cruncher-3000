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
 * Interface Order.
 */
interface Order
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return AssetPair
     */
    public function getPair(): AssetPair;

    /**
     * @param array $spec
     *
     * @throws \InvalidArgumentException
     *
     * @return Transaction
     */
    public function addTransactionFromLoaderSpec(array $spec): Transaction;
}
