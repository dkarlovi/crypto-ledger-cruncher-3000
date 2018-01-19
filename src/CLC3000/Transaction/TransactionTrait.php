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
use Dkarlovi\CLC3000\Wallet\Amount\AmountSegment;
use Dkarlovi\CLC3000\Wallet\Amount\SegmentedAmount;

/**
 * Trait TransactionTrait.
 */
trait TransactionTrait
{
    /**
     * @var Order
     */
    private $order;

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
     * @return Amount
     */
    public function getWithdrawalAmount(): Amount
    {
        if ($this->order instanceof Order\SellOrder) {
            // selling = withdrawing crypto
            return new SegmentedAmount(
                [
                    new AmountSegment($this->volume, $this->cost),
                ]
            );
        }

        // buying = withdrawing fiat
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
        if ($this->order instanceof Order\SellOrder) {
            // selling = depositing fiat
            return new SegmentedAmount(
                [
                    new AmountSegment($this->cost, $this->volume),
                ]
            );
        }

        // buying = depositing crypto
        return new SegmentedAmount(
            [
                new AmountSegment($this->volume, $this->cost),
            ]
        );
    }
}
