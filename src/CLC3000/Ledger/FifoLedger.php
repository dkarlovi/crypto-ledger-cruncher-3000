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

        // dummy initial state
        $btcWallet = new Wallet\BasicWallet(new Asset\CryptoAsset(Asset::CRYPTO_BTC));
        $btcWallet->deposit(new Wallet\Amount\SegmentedAmount([
            // bought 0.07462 BTC for 100 EUR
            new Wallet\Amount\AmountSegment(0.07462, 100.0),
        ]));
        $this->wallets[Asset::CRYPTO_BTC] = $btcWallet;
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
     * @return Wallet[]
     */
    public function getStatus(): array
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

            $this->orders[$id] = new $class($id, $type, $pair);
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
        $fromAsset = $pair->getFrom();
        $toWallet = $this->getWallet($pair->getTo(), true);

        switch (\get_class($order)) {
            case Order\BuyOrder::class:
                try {
                    $fromWallet = $this->getWallet($fromAsset);
                } catch (\RuntimeException $exception) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'TX %1$s: buying %2$s %3$s: %4$s',
                            $transaction->getId(),
                            $transaction->getDepositAmount()->getTotal(),
                            $fromAsset->getCode(),
                            $exception->getMessage()
                        ),
                        0,
                        $exception
                    );
                }
                break;
            case Order\SellOrder::class:
                try {
                    $fromWallet = $this->getWallet($pair->getFrom());
                } catch (\RuntimeException $exception) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'TX %1$s: selling %2$s %3$s: %4$s',
                            $transaction->getId(),
                            $transaction->getWithdrawalAmount()->getTotal(),
                            $fromAsset->getCode(),
                            $exception->getMessage()
                        ),
                        0,
                        $exception
                    );
                }
                break;
            default:
                throw new \InvalidArgumentException('Unknown order type');
        }

        $fromWallet->withdraw($transaction->getWithdrawalAmount());
        $toWallet->deposit($transaction->getDepositAmount());
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

            $this->wallets[$code] = new Wallet\BasicWallet($asset);
        }

        return $this->wallets[$code];
    }
}
