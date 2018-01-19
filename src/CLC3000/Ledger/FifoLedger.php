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
use Dkarlovi\CLC3000\Ledger;
use Dkarlovi\CLC3000\Loader;
use Dkarlovi\CLC3000\Order;
use Dkarlovi\CLC3000\Transaction;
use Dkarlovi\CLC3000\Wallet;

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
     * @var Wallet[]
     */
    private $wallets = [];

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
     * @param mixed $source
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function load($source): void
    {
        $this->loader->load($source, [$this, 'addTransactionFromLoaderSpec']);
    }

    /**
     * @return Wallet[]
     */
    public function getWallets(): array
    {
        return array_values($this->wallets);
    }

    /**
     * @param array $spec
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function addTransactionFromLoaderSpec(array $spec): void
    {
        $id = $spec[Loader::ORDER];
        if (false === array_key_exists($id, $this->orders)) {
            $class = $spec[Loader::ORDER_CLASS];
            $type = $spec[Loader::ORDER_TYPE];
            $pair = $spec[Loader::ORDER_PAIR];

            $this->orders[$id] = new $class($id, $pair, $type);
        }

        $order = $this->orders[$id];
        $transaction = $order->addTransactionFromLoaderSpec($spec);
        $this->adjustWallets($order, $transaction);
    }

    /**
     * @param Order       $order
     * @param Transaction $transaction
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    private function adjustWallets(Order $order, Transaction $transaction)
    {
        $pair = $order->getPair();
        $toWallet = $this->getWallet($pair->getTo(), true);

        switch (\get_class($order)) {
            case Order\DepositOrder::class:
                $fiatAsset = $pair->getTo();
                $fiatAmount = $transaction->getDepositAmount();
                $cryptoAsset = $pair->getTo();
                $cryptoAmount = $transaction->getDepositAmount();
                $description = 'depositing';
                break;
            case Order\BuyOrder::class:
                $fiatAsset = $pair->getFrom();
                $fiatAmount = $transaction->getWithdrawalAmount();
                $cryptoAsset = $pair->getTo();
                $cryptoAmount = $transaction->getDepositAmount();
                $description = 'buying';
                break;
            case Order\SellOrder::class:
                $fiatAsset = $pair->getTo();
                $fiatAmount = $transaction->getDepositAmount();
                $cryptoAsset = $pair->getFrom();
                $cryptoAmount = $transaction->getWithdrawalAmount();
                $description = 'selling';
                break;
            default:
                throw new \InvalidArgumentException(
                    sprintf(
                        'TX %1$s: invalid transaction type "%2$s"',
                        $transaction->getId(),
                        \get_class($order)
                    )
                );
        }

        $fromAsset = $pair->getFrom();
        try {
            $fromWallet = $this->getWallet($fromAsset);
        } catch (\RuntimeException $exception) {
            throw new \InvalidArgumentException(
                sprintf(
                    'TX %1$s: %2$s %3$s %4$s for %5$s %6$s: %7$s',
                    $transaction->getId(),
                    $description,
                    $cryptoAsset->getCode(),
                    $cryptoAmount->getTotal(),
                    $fiatAsset->getCode(),
                    $fiatAmount->getTotal(),
                    $exception->getMessage()
                ),
                0,
                $exception
            );
        }

        if ($transaction->isWithdrawalTransaction()) {
            try {
                $fromWallet->withdraw($transaction->getWithdrawalAmount());
            } catch (\RuntimeException $exception) {
                throw new \RuntimeException(
                    sprintf(
                        'TX %1$s: withdraw failed when %2$s %3$s %4$s for %5$s %6$s: %7$s',
                        $transaction->getId(),
                        $description,
                        $cryptoAsset->getCode(),
                        $cryptoAmount->getTotal(),
                        $fiatAsset->getCode(),
                        $fiatAmount->getTotal(),
                        $exception->getMessage()
                    ),
                    0,
                    $exception
                );
            }
        }

        if ($transaction->isDepositTransaction()) {
            try {
                $toWallet->deposit($transaction->getDepositAmount());
            } catch (\RuntimeException $exception) {
                throw new \RuntimeException(
                    sprintf(
                        'TX %1$s: deposit failed when %2$s %3$s %4$s for %5$s %6$s: %7$s',
                        $transaction->getId(),
                        $description,
                        $cryptoAsset->getCode(),
                        $cryptoAmount->getTotal(),
                        $fiatAsset->getCode(),
                        $fiatAmount->getTotal(),
                        $exception->getMessage()
                    ),
                    0,
                    $exception
                );
            }
        }
    }

    /**
     * @param Asset $asset
     * @param bool  $autoCreate
     *
     * @throws \RuntimeException
     *
     * @return Wallet
     */
    private function getWallet(Asset $asset, $autoCreate = false): Wallet
    {
        $code = $asset->getCode();
        if (false === array_key_exists($asset->getCode(), $this->wallets)) {
            if (false === $autoCreate) {
                throw new \RuntimeException(sprintf('Fetching an non-existing %1$s wallet', $code));
            }

            if ($asset instanceof Asset\CryptoAsset) {
                $this->wallets[$code] = new Wallet\SegmentedWallet($asset);
            } else {
                $this->wallets[$code] = new Wallet\BasicWallet($asset);
            }
        }

        return $this->wallets[$code];
    }
}
