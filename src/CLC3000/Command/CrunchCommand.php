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

namespace Dkarlovi\CLC3000\Command;

use Dkarlovi\CLC3000\File\File;
use Dkarlovi\CLC3000\Ledger\FifoLedger;
use Dkarlovi\CLC3000\Loader;
use Dkarlovi\CLC3000\Loader\KrakenLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CrunchCommand.
 */
class CrunchCommand extends Command
{
    private static $exchanges = [
        'kraken' => KrakenLoader::class,
    ];

    /**
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure(): void
    {
        $this
            ->setName('crunch')
            ->setDescription('Crypto exchange ledger reader and gain/loss calculator')
            ->addArgument('exchange', InputArgument::REQUIRED, 'Exchange this ledger is from')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to the access log')
        ;
    }

    /***
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $exchange = $input->getArgument('exchange');

        /** @var Loader $loader */
        $loader = new self::$exchanges[$exchange]();
        $ledger = new FifoLedger($loader);

        $path = $input->getArgument('path');
        try {
            $ledger->load(new File($path));
        } catch (\Exception $exception) {
            throw new InvalidArgumentException($exception->getMessage(), $exception->getCode(), $exception);
        }

        $table = new Table($output);
        $table->setHeaders(['Asset', 'Volume']);
        foreach ($ledger->getWallets() as $wallet) {
            $table->addRow([$wallet->getAsset()->getCode(), $wallet->getTotal()]);
        }
        $table->render();
    }
}
