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
 * Interface Asset.
 */
interface Asset
{
    public const CRYPTO_BCH = 'BCH';
    public const CRYPTO_BTC = 'BTC';
    public const CRYPTO_ETC = 'ETC';
    public const CRYPTO_ETH = 'ETH';
    public const CRYPTO_LTC = 'LTC';
    public const CRYPTO_MNR = 'MNR';
    public const CRYPTO_REP = 'REP';
    public const CRYPTO_XRP = 'XRP';
    public const CRYPTO_ZEC = 'ZEC';

    public const FIAT_EUR = 'EUR';

    /**
     * @return string
     */
    public function getCode(): string;

    /**
     * @param Asset $asset
     *
     * @return bool
     */
    public function equals(self $asset): bool;
}
