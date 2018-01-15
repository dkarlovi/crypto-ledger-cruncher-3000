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
    public const ORDER_CLASS = 'order_class';
    public const ORDER_TYPE = 'order_type';
    public const ORDER_PAIR = 'order_pair';
    public const TRANSACTION = 'transaction';
    public const ORDER = 'order';
    public const TIME = 'time';
    public const PRICE = 'price';
    public const COST = 'cost';
    public const FEE = 'fee';
    public const VOLUME = 'volume';

    /**
     * @param File   $file
     * @param Ledger $ledger
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function loadIntoLedger(File $file, Ledger $ledger): void;
}
