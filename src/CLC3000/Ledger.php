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
     * @param mixed $source
     */
    public function load($source): void;

    /**
     * @return Wallet[]
     */
    public function getWallets(): array;
}
