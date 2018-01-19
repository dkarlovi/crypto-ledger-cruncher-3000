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

namespace Dkarlovi\CLC3000\Loader;

use Dkarlovi\CLC3000\Loader;

/**
 * Class DummyLoader.
 */
class DummyLoader implements Loader
{
    /**
     * @var callable
     */
    private $preprocessor;

    /**
     * @param callable $preprocessor
     */
    public function __construct(callable $preprocessor)
    {
        $this->preprocessor = $preprocessor;
    }

    /**
     * @param array    $source
     * @param callable $processor
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return void
     */
    public function load($source, callable $processor): void
    {
        foreach ($source as $item) {
            $processor(\call_user_func($this->preprocessor, $item));
        }
    }
}
