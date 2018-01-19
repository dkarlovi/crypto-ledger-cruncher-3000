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

namespace Dkarlovi\CLC3000\Tests;

use Dkarlovi\CLC3000\Asset;
use Dkarlovi\CLC3000\AssetPair;
use Dkarlovi\CLC3000\Ledger;
use Dkarlovi\CLC3000\Loader;
use Dkarlovi\CLC3000\Loader\DummyLoader;
use Dkarlovi\CLC3000\Order\BuyOrder;
use Dkarlovi\CLC3000\Order\DepositOrder;
use Dkarlovi\CLC3000\Order\SellOrder;
use Dkarlovi\CLC3000\Wallet;
use PHPUnit\Framework\TestCase;

/**
 * Class FunctionalTestCase.
 */
abstract class FunctionalTestCase extends TestCase
{
    /**
     * @return array
     */
    abstract public function getDataProvider(): array;

    /**
     * @dataProvider getDataProvider
     *
     * @param array $fixtures
     * @param array $assets
     */
    public function testLedger(array $fixtures, array $assets)
    {
        $ledger = $this->createLedger($fixtures);

        static::assertAssets($assets, $ledger->getWallets());
    }

    /**
     * @param array<float, string> $expected
     * @param Wallet[]             $wallets
     */
    public static function assertAssets(array $expected, array $wallets)
    {
        $actual = [];
        foreach ($wallets as $wallet) {
            $actual[$wallet->getAsset()->getCode()] = $wallet->getTotal();
        }

        static::assertEquals($expected, $actual);
    }

    /**
     * @param array $fixtures
     *
     * @return Ledger
     */
    abstract protected function createLedger(array $fixtures): Ledger;

    /**
     * @return Loader
     */
    protected function createLoader(): Loader
    {
        $keys = [
            Loader::ORDER,
            Loader::TRANSACTION,
            Loader::ORDER_TYPE,
            Loader::ORDER_PAIR,
            Loader::VOLUME,
            Loader::COST,
            Loader::FEE,
            Loader::PRICE,
        ];

        return new DummyLoader(function (array $row) use ($keys) {
            $total = \count($keys);
            $items = \count($row);
            $missing = $total - $items;
            $row = \array_combine($keys, \array_merge($row, array_fill(0, $missing, null)));

            switch ($row[Loader::ORDER_TYPE]) {
                case 'deposit':
                    $row[Loader::ORDER_CLASS] = DepositOrder::class;
                    break;
                case 'buy':
                    $row[Loader::ORDER_CLASS] = BuyOrder::class;
                    break;
                case 'sell':
                    $row[Loader::ORDER_CLASS] = SellOrder::class;
                    break;
                default:
                    throw new \RuntimeException('Unknown order type');
            }
            $row[Loader::ORDER_PAIR] = $this->parsePair($row[Loader::ORDER_PAIR]);

            // TODO: time-related things?
            $row[Loader::TIME] = new \DateTimeImmutable();

            return $row;
        });
    }

    /**
     * @param string $pair
     *
     * @return AssetPair
     */
    private function parsePair(string $pair): AssetPair
    {
        // TODO: this might have side-effects
        if (false === mb_strpos($pair, '-')) {
            return new Asset\AssetPair(new Asset\CryptoAsset($pair), new Asset\CryptoAsset($pair));
        }
        [$from, $to] = explode('-', $pair);

        return new Asset\AssetPair(new Asset\CryptoAsset($from), new Asset\CryptoAsset($to));
    }
}
