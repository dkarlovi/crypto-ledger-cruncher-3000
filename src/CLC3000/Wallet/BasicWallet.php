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

use Dkarlovi\CLC3000\Amount;
use Dkarlovi\CLC3000\Asset;
use Dkarlovi\CLC3000\Wallet;
use Dkarlovi\CLC3000\Wallet\Amount\SegmentedAmount;

/**
 * Class BasicWallet.
 */
class BasicWallet implements Wallet
{
    /**
     * @var Asset
     */
    private $asset;

    /**
     * @var Amount
     */
    private $amount;

    /**
     * @param Asset $asset
     */
    public function __construct(Asset $asset)
    {
        $this->asset = $asset;
        $this->amount = new SegmentedAmount([]);
    }

    /**
     * @inheritdoc
     */
    public function getAsset(): Asset
    {
        return $this->asset;
    }

    /**
     * @inheritdoc
     */
    public function getTotal(): float
    {
        return $this->amount->getTotal();
    }

    /**
     * @inheritdoc
     */
    public function deposit(Amount $amount): float
    {
        $this->amount->increment($amount);

        return $this->amount->getTotal();
    }

    /**
     * @inheritdoc
     */
    public function withdraw(Amount $amount): float
    {
        $this->amount->decrement($amount);

        return $this->amount->getTotal();
    }
}
