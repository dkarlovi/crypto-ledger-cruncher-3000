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

namespace Dkarlovi\CLC3000\File;

/**
 * Class File.
 */
class File extends \SplFileInfo
{
    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($path)
    {
        if (false === file_exists($path)) {
            throw new \InvalidArgumentException(\sprintf('File at path %1$s does not exist', $path));
        }

        parent::__construct($path);
    }
}
