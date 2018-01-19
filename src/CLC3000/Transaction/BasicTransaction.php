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
use Dkarlovi\CLC3000\Wallet\Amount\AmountSegment;
use Dkarlovi\CLC3000\Wallet\Amount\SegmentedAmount;

/**
 * Class SimpleTransaction.
 */
class BasicTransaction implements Transaction
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var \DateTimeInterface
     */
    private $time;

    /**
     * @var float
     */
    private $price;

    /**
     * @var float
     */
    private $cost;

    /**
     * @var float
     */
    private $fee;

    /**
     * @var float
     */
    private $volume;

    /**
     * @param string             $id
     * @param Order              $order
     * @param \DateTimeInterface $time
     * @param float              $price
     * @param float              $cost
     * @param float              $fee
     * @param float              $volume
     */
    public function __construct(
        string $id,
        Order $order,
        \DateTimeInterface $time,
        float $price,
        float $cost,
        float $fee,
        float $volume
    ) {
        $this->id = $id;
        $this->order = $order;
        $this->time = $time;
        $this->price = $price;
        $this->cost = $cost;
        $this->fee = $fee;
        $this->volume = $volume;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return Amount
     */
    public function getWithdrawalAmount(): Amount
    {
        return new SegmentedAmount(
            [
                new AmountSegment($this->cost, $this->volume),
            ]
        );
    }

    /**
     * @return Amount
     */
    public function getDepositAmount(): Amount
    {
        return new SegmentedAmount(
            [
                new AmountSegment($this->volume, $this->cost),
            ]
        );
    }
}
