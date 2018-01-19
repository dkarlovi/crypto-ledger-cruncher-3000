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

namespace Dkarlovi\CLC3000\Asset;

use Dkarlovi\CLC3000\Asset;

/**
 * Class CryptoAsset.
 */
class CryptoAsset implements Asset
{
    /**
     * @var string
     */
    private $asset;

    /**
     * @param string $asset
     */
    public function __construct(string $asset)
    {
        $this->asset = $asset;
    }

    /**
     * @param Asset $asset
     *
     * @return bool
     */
    public function equals(Asset $asset): bool
    {
        return $asset->getCode() === $this->asset;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->asset;
    }
}
