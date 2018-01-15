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

namespace Dkarlovi\CLC3000\Ledger;

use Dkarlovi\CLC3000\Ledger;
use Dkarlovi\CLC3000\Loader;
use Dkarlovi\CLC3000\Order;
use Dkarlovi\CLC3000\Transaction;

/**
 * Class SimpleLedger.
 */
class FifoLedger implements Ledger
{
    /** @var Order[] */
    private $orders = [];

    /**
     * @return Order[]
     */
    public function getOrders(): array
    {
        // TODO: Implement getOrders() method.
    }

    /**
     * @return Transaction[]
     */
    public function getTransactions(): array
    {
        // TODO: Implement getTransactions() method.
    }

    /**
     * @param Transaction $transaction
     */
    public function addTransaction(Transaction $transaction): void
    {
        // TODO: Implement addTransaction() method.
    }

    /**
     * @param array $spec
     */
    public function addTransactionFromLoaderSpec(array $spec): void
    {
        $id = $spec[Loader::ORDER];
        if (false === array_key_exists($id, $this->orders)) {
            $orderClass = $spec[Loader::ORDER_CLASS];

            $order = new $orderClass($id);
            $this->orders[$id] = $order;
        }

        $this->orders[$id]->addTransactionFromLoaderSpec($spec);
    }
}
