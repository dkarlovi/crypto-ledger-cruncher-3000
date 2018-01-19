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

use Dkarlovi\CLC3000\File\File;

/**
 * Interface Ledger.
 */
interface Ledger
{
    /**
     * @param File $file
     */
    public function load(File $file): void;

    /**
     * @return Order[]
     */
    public function getOrders(): array;

    /**
     * @return Asset[]
     */
    public function getAssets(): array;
}
