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

/**
 * Trait WalletTrait.
 */
trait WalletTrait
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
     *
     * @throws \RuntimeException
     */
    public function deposit(Amount $amount): float
    {
        try {
            $this->amount->increment($amount);
        } catch (\InvalidArgumentException $exception) {
            throw new \RuntimeException(
                sprintf(
                    'Depositing %1$s %2$s: %3$s',
                    $amount->getTotal(),
                    $this->asset->getCode(),
                    $exception->getMessage()
                ),
                0,
                $exception
            );
        }

        return $this->amount->getTotal();
    }

    /**
     * @inheritdoc
     *
     * @throws \RuntimeException
     */
    public function withdraw(Amount $amount): float
    {
        try {
            $this->amount->decrement($amount);
        } catch (\InvalidArgumentException $exception) {
            throw new \RuntimeException(
                sprintf(
                    'Withdrawing %1$s %2$s: %3$s',
                    $amount->getTotal(),
                    $this->asset->getCode(),
                    $exception->getMessage()
                ),
                0,
                $exception
            );
        }

        return $this->amount->getTotal();
    }
}
