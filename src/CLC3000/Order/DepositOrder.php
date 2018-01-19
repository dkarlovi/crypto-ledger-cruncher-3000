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

namespace Dkarlovi\CLC3000\Order;

use Dkarlovi\CLC3000\AssetPair;
use Dkarlovi\CLC3000\Loader;
use Dkarlovi\CLC3000\Order;
use Dkarlovi\CLC3000\Transaction;

/**
 * Class Deposit.
 */
class DepositOrder implements Order
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var AssetPair
     */
    private $pair;

    /**
     * @param string    $id
     * @param AssetPair $pair
     */
    public function __construct(string $id, AssetPair $pair)
    {
        $this->id = $id;
        $this->pair = $pair;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return AssetPair
     */
    public function getPair(): AssetPair
    {
        return $this->pair;
    }

    /**
     * @param array $spec
     *
     * @throws \InvalidArgumentException
     *
     * @return Transaction
     */
    public function addTransactionFromLoaderSpec(array $spec): Transaction
    {
        $transaction = new Transaction\DepositTransaction(
            $spec[Loader::TRANSACTION],
            $this,
            $spec[Loader::TIME],
            $spec[Loader::COST],
            $spec[Loader::FEE],
            $spec[Loader::VOLUME]
        );

        return $transaction;
    }
}
