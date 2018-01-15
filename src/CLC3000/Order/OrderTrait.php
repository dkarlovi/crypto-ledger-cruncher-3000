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
use Dkarlovi\CLC3000\LedgerLoader;
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
     * @var AssetPair
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
     * @return AssetPair
     */
    public function getPair(): AssetPair
    {
        return $this->pair;
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
        if (static::class !== $spec[LedgerLoader::ORDER_CLASS]) {
            throw new \InvalidArgumentException('Order type changed');
        }
        if ($this->type !== $spec[LedgerLoader::ORDER_TYPE]) {
            throw new \InvalidArgumentException('Order type changed');
        }
        if (false === $this->pair->equals($spec[LedgerLoader::ORDER_PAIR])) {
            throw new \InvalidArgumentException('Order pair changed');
        }

        $this->setStart($spec[LedgerLoader::TIME]);
        $this->setEnd($spec[LedgerLoader::TIME]);
    }

    /**
     * @param array $spec
     *
     * @return Transaction
     */
    private function addTransactionFromSpec(array $spec): Transaction
    {
        $transaction = new Transaction\BasicTransaction(
            $spec[LedgerLoader::TRANSACTION],
            $this,
            $spec[LedgerLoader::TIME],
            $spec[LedgerLoader::PRICE],
            $spec[LedgerLoader::COST],
            $spec[LedgerLoader::FEE],
            $spec[LedgerLoader::VOLUME]
        );

        $this->transactions[] = $transaction;

        return $transaction;
    }
}
