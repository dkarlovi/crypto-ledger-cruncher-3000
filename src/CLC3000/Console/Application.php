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

namespace Dkarlovi\CLC3000\Console;

use Dkarlovi\CLC3000\Command\CrunchCommand;
use Symfony\Component\Console\Application as BaseApplication;

/**
 * Class Application.
 */
class Application extends BaseApplication
{
    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    protected function getDefaultCommands()
    {
        /** @var \Symfony\Component\Console\Command\Command[] $commands */
        $commands = array_merge(
            parent::getDefaultCommands(),
            [
                new CrunchCommand(),
            ]
        );
        $this->setDefaultCommand('crunch');

        return $commands;
    }
}
