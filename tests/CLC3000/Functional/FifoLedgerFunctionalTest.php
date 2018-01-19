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

namespace Dkarlovi\CLC3000\Tests\Functional;

use Dkarlovi\CLC3000\Ledger;
use Dkarlovi\CLC3000\Tests\FunctionalTestCase;

/**
 * Class FifoLedgerFunctionalTest.
 */
class FifoLedgerFunctionalTest extends FunctionalTestCase
{
    /**
     * @return array
     */
    public function getDataProvider(): array
    {
        // order, transaction, type, pair, volume, cost, fee
        return [
            [
                [
                    ['or1', 'tx1', 'deposit', 'EUR', 95, 100, 5],
                ],
                ['EUR' => 95],
            ],
            [
                [
                    ['or1', 'tx1', 'deposit', 'EUR', 95, 100, 5],
                    ['or1', 'tx2', 'deposit', 'EUR', 1500, 1500, 0],
                ],
                ['EUR' => 1595],
            ],
        ];
    }

    /**
     * @param array $fixtures
     *
     * @return Ledger
     */
    protected function createLedger(array $fixtures): Ledger
    {
        $ledger = new Ledger\FifoLedger($this->createLoader());
        $ledger->load($fixtures);

        return $ledger;
    }
}
