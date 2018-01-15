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
 * Interface Loader.
 */
interface Loader
{
    public const TRANSACTION = 'transaction';
    public const ORDER = 'order';
    public const ORDER_CLASS = 'order_class';

    /**
     * @param File   $file
     * @param Ledger $ledger
     *
     * @return void
     */
    public function loadIntoLedger(File $file, Ledger $ledger): void;
}
