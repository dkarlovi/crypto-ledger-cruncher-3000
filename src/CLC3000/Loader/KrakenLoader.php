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

namespace Dkarlovi\CLC3000\Loader;

use Dkarlovi\CLC3000\Asset;
use Dkarlovi\CLC3000\AssetPair;
use Dkarlovi\CLC3000\File\File;
use Dkarlovi\CLC3000\Loader;
use Dkarlovi\CLC3000\Order\BuyOrder;
use Dkarlovi\CLC3000\Order\DepositOrder;
use Dkarlovi\CLC3000\Order\SellOrder;

/**
 * Class KrakenLoader.
 */
class KrakenLoader implements Loader
{
    private const KRAKEN_TRANSACTION = 'txid';
    private const KRAKEN_ORDER = 'ordertxid';
    private const KRAKEN_PAIR = 'pair';
    private const KRAKEN_TIME = 'time';
    private const KRAKEN_TYPE = 'type';
    private const KRAKEN_ORDER_TYPE = 'ordertype';
    private const KRAKEN_PRICE = 'price';
    private const KRAKEN_COST = 'cost';
    private const KRAKEN_FEE = 'fee';
    private const KRAKEN_VOLUME = 'vol';
    private const KRAKEN_MARGIN = 'margin';
    private const KRAKEN_MISC = 'misc';
    private const KRAKEN_LEDGERS = 'ledgers';

    private const KRAKEN_BUY = 'buy';
    private const KRAKEN_SELL = 'sell';
    private const KRAKEN_DEPOSIT = 'deposit';

    private static $count = 13;
    private static $header = [
        self::KRAKEN_TRANSACTION,
        self::KRAKEN_ORDER,
        self::KRAKEN_PAIR,
        self::KRAKEN_TIME,
        self::KRAKEN_TYPE,
        self::KRAKEN_ORDER_TYPE,
        self::KRAKEN_PRICE,
        self::KRAKEN_COST,
        self::KRAKEN_FEE,
        self::KRAKEN_VOLUME,
        self::KRAKEN_MARGIN,
        self::KRAKEN_MISC,
        self::KRAKEN_LEDGERS,
    ];

    private static $crypto = [
        'BCH' => Asset::CRYPTO_BCH,
        'XXBT' => Asset::CRYPTO_BTC,
        'XETC' => Asset::CRYPTO_ETC,
        'XETH' => Asset::CRYPTO_ETH,
        'XLTC' => Asset::CRYPTO_LTC,
        'XXMR' => Asset::CRYPTO_MNR,
        'XREP' => Asset::CRYPTO_REP,
        'XXRP' => Asset::CRYPTO_XRP,
        'XZEC' => Asset::CRYPTO_ZEC,
    ];

    private static $fiat = [
        'ZEUR' => Asset::FIAT_EUR,
        'EUR' => Asset::FIAT_EUR,
    ];

    /**
     * @inheritdoc
     *
     * @param File $file
     */
    public function load($file, callable $processor): void
    {
        $header = false;
        $handle = fopen($file->getPathname(), 'rb');
        while (($row = fgetcsv($handle)) !== false) {
            if (false === $header) {
                $header = true;
                $this->validateHeader($row);
                continue;
            }

            // loader compatible spec
            $spec = $this->createLoaderSpec($row);
            $processor($spec);
        }

        fclose($handle);
    }

    /**
     * @param string[] $row
     *
     * @throws \InvalidArgumentException
     */
    private function validateHeader(array $row)
    {
        if ($row !== self::$header) {
            throw new \InvalidArgumentException('Invalid Kraken archive');
        }
    }

    /**
     * @param string[] $row
     *
     * @throws \InvalidArgumentException
     *
     * @return array<string|int|float, string>
     */
    private function createLoaderSpec(array $row): array
    {
        if (self::$count !== \count($row)) {
            throw new \InvalidArgumentException('Invalid Kraken archive');
        }

        $row = \array_combine(self::$header, $row);

        // TODO: convert to a DTO
        $out = [
            self::ORDER_TYPE => $row[self::KRAKEN_ORDER_TYPE],
            self::TRANSACTION => $row[self::KRAKEN_TRANSACTION],
            self::ORDER => $row[self::KRAKEN_ORDER],
            self::TIME => new \DateTimeImmutable($row[self::KRAKEN_TIME], new \DateTimeZone('UTC')),
            self::PRICE => (float) $row[self::KRAKEN_PRICE],
            self::COST => (float) $row[self::KRAKEN_COST],
            self::FEE => (float) $row[self::KRAKEN_FEE],
            self::VOLUME => (float) $row[self::KRAKEN_VOLUME],
        ];
        switch ($row[self::KRAKEN_TYPE]) {
            case self::KRAKEN_DEPOSIT:
                $out[self::ORDER_CLASS] = DepositOrder::class;
                break;
            case self::KRAKEN_BUY:
                $out[self::ORDER_CLASS] = BuyOrder::class;
                break;
            case self::KRAKEN_SELL:
                $out[self::ORDER_CLASS] = SellOrder::class;
                break;
            default:
                throw new \InvalidArgumentException(
                    sprintf('Invalid Kraken archive: invalid order direction "%1$s"', $row[self::KRAKEN_TYPE])
                );
        }

        $out[self::ORDER_PAIR] = $this->normalizePair($row[self::KRAKEN_PAIR], $row[self::KRAKEN_TYPE]);

        return $out;
    }

    /**
     * Transforms "XXBTZEUR", sell to AssetPair(Asset('BTC'), Asset('EUR')).
     * Transforms "XXBTZEUR", buy  to AssetPair(Asset('EUR'), Asset('BTC')).
     *
     * Note that it's always "<Crypto><Fiat>", direction is used to determine what's happening.
     *
     * @param string $pair
     * @param string $direction "buy" or "sell" (crypto for fiat)
     *
     * @throws \InvalidArgumentException
     *
     * @return AssetPair
     */
    private function normalizePair(string $pair, string $direction): AssetPair
    {
        $assets = [];
        foreach (self::$crypto as $alias => $name) {
            if (0 === mb_strpos($pair, $alias)) {
                $assets[] = new Asset\CryptoAsset($name);
                $pair = \mb_substr($pair, \mb_strlen($alias));
                continue;
            }
        }
        foreach (self::$fiat as $alias => $name) {
            if (0 === mb_strpos($pair, $alias)) {
                $assets[] = new Asset\FiatAsset($name);
                $pair = \mb_substr($pair, \mb_strlen($alias));
                continue;
            }
        }
        if ($pair) {
            throw new \InvalidArgumentException(
                sprintf('Invalid Kraken archive: invalid asset pair "%1$s"', $pair)
            );
        }

        switch ($direction) {
            case self::KRAKEN_DEPOSIT:
                $assetPair = new Asset\AssetPair($assets[0], $assets[0]);
                break;
            case self::KRAKEN_BUY:
                $assetPair = new Asset\AssetPair($assets[1], $assets[0]);
                break;
            case self::KRAKEN_SELL:
                $assetPair = new Asset\AssetPair($assets[0], $assets[1]);
                break;
            default:
                throw new \InvalidArgumentException(
                    sprintf('Invalid Kraken archive: invalid order direction "%1$s"', $direction)
                );
        }

        return $assetPair;
    }
}
