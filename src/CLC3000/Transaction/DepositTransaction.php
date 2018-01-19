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

namespace Dkarlovi\CLC3000\Transaction;

use Dkarlovi\CLC3000\Amount;
use Dkarlovi\CLC3000\Order;
use Dkarlovi\CLC3000\Transaction;

/**
 * Class DepositTransaction.
 */
class DepositTransaction implements Transaction
{
    use TransactionTrait {
        getWithdrawalAmount as private _getWithdrawalAmount;
    }

    /**
     * @var string
     */
    private $id;

    /**
     * @var \DateTimeInterface
     */
    private $time;

    /**
     * @param string             $id
     * @param Order              $order
     * @param \DateTimeInterface $time
     * @param float              $cost
     * @param float              $fee
     * @param float              $volume
     */
    public function __construct(
        string $id,
        Order $order,
        \DateTimeInterface $time,
        float $cost,
        float $fee,
        float $volume
    ) {
        $this->id = $id;
        $this->order = $order;
        $this->time = $time;
        $this->cost = $cost;
        $this->fee = $fee;
        $this->volume = $volume;
    }

    /**
     * @inheritdoc
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function isWithdrawalTransaction(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getWithdrawalAmount(): Amount
    {
        throw new \RuntimeException('Not a withdrawal transaction');
    }

    /**
     * @inheritdoc
     */
    public function isDepositTransaction(): bool
    {
        return true;
    }
}
