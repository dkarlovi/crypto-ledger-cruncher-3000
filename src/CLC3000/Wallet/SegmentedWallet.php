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

namespace Dkarlovi\CLC3000\Wallet;

use Dkarlovi\CLC3000\Asset;
use Dkarlovi\CLC3000\Wallet;
use Dkarlovi\CLC3000\Wallet\Amount\SegmentedAmount;

/**
 * Class SegmentedWallet.
 */
class SegmentedWallet implements Wallet
{
    use WalletTrait;

    /**
     * @param Asset $asset
     */
    public function __construct(Asset $asset)
    {
        $this->asset = $asset;
        $this->amount = new SegmentedAmount([]);
    }
}
