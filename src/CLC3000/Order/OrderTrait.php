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

use Dkarlovi\CLC3000\Loader;
use Dkarlovi\CLC3000\Transaction;

/**
 * Trait OrderTrait.
 */
trait OrderTrait
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var Transaction[]
     */
    protected $transactions = [];

    /**
     * @var \DateTimeInterface
     */
    protected $start;

    /**
     * @var \DateTimeInterface
     */
    protected $end;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $pair;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function addTransactionFromLoaderSpec(array $spec): Transaction
    {
        $this->setMetadata($spec);

        $transaction = $this->addTransactionFromSpec($spec);

        return $transaction;
    }

    /**
     * @param \DateTimeInterface $time
     */
    protected function setStart(\DateTimeInterface $time): void
    {
        if (null === $this->start || $time < $this->start) {
            $this->start = $time;
        }
    }

    /**
     * @param \DateTimeInterface $time
     */
    protected function setEnd(\DateTimeInterface $time): void
    {
        if (null === $this->end || $time > $this->end) {
            $this->end = $time;
        }
    }

    /**
     * @param array $spec
     *
     * @throws \InvalidArgumentException
     */
    private function setMetadata(array $spec): void
    {
        // TODO: better error messages
        if (static::class !== $spec[Loader::ORDER_CLASS]) {
            throw new \InvalidArgumentException('Order type changed');
        }
        if ($this->type !== $spec[Loader::ORDER_TYPE]) {
            throw new \InvalidArgumentException('Order type changed');
        }
        if ($this->pair !== $spec[Loader::ORDER_PAIR]) {
            throw new \InvalidArgumentException('Order pair changed');
        }

        $this->setStart($spec[Loader::TIME]);
        $this->setEnd($spec[Loader::TIME]);
    }

    /**
     * @param array $spec
     *
     * @return Transaction
     */
    private function addTransactionFromSpec(array $spec): Transaction
    {
        $transaction = new Transaction\BasicTransaction(
            $spec[Loader::TRANSACTION],
            $this,
            $spec[Loader::TIME],
            $spec[Loader::PRICE],
            $spec[Loader::COST],
            $spec[Loader::FEE],
            $spec[Loader::VOLUME]
        );

        $this->transactions[] = $transaction;

        return $transaction;
    }
}
