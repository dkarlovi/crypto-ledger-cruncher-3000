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

use Dkarlovi\CLC3000\Asset;
use Dkarlovi\CLC3000\File\File;
use Dkarlovi\CLC3000\Ledger;
use Dkarlovi\CLC3000\Loader;
use Dkarlovi\CLC3000\Order;
use Dkarlovi\CLC3000\Transaction;

/**
 * Class SimpleLedger.
 */
class FifoLedger implements Ledger
{
    /**
     * @var Order[]
     */
    private $orders = [];

    /**
     * @var Asset[]
     */
    private $assets = [];

    /**
     * @var Loader
     */
    private $loader;

    /**
     * @param Loader $loader
     */
    public function __construct(Loader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @param File $file
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function load(File $file): void
    {
        $this->loader->load($file, [$this, 'addTransactionFromLoaderSpec']);
    }

    /**
     * @return Order[]
     */
    public function getOrders(): array
    {
        return $this->orders;
    }

    /**
     * @return Asset[]
     */
    public function getAssets(): array
    {
        return $this->assets;
    }

    /**
     * @param array $spec
     *
     * @throws \InvalidArgumentException
     */
    public function addTransactionFromLoaderSpec(array $spec): void
    {
        $id = $spec[Loader::ORDER];
        if (false === array_key_exists($id, $this->orders)) {
            $class = $spec[Loader::ORDER_CLASS];
            $type = $spec[Loader::ORDER_TYPE];
            $pair = $spec[Loader::ORDER_PAIR];

            $this->orders[$id] = new $class($id, $type, $pair);
        }

        $order = $this->orders[$id];
        $transaction = $order->addTransactionFromLoaderSpec($spec);
        $this->adjustAssets($order, $transaction);
    }

    /**
     * @param Order       $order
     * @param Transaction $transaction
     *
     * @throws \InvalidArgumentException
     */
    private function adjustAssets(Order $order, Transaction $transaction)
    {
        switch (\get_class($order)) {
            case Order\BuyOrder::class:
                break;
            case Order\SellOrder::class:
                break;
            default:
                throw new \InvalidArgumentException('Unknown order type');
        }
    }
}
