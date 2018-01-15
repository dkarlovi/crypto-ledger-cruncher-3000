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

namespace Dkarlovi\CLC3000;

/**
 * Interface AssetPair.
 */
interface AssetPair
{
    /**
     * @return Asset
     */
    public function getFrom(): Asset;

    /**
     * @return Asset
     */
    public function getTo(): Asset;

    /**
     * @param AssetPair $pair
     *
     * @return bool
     */
    public function equals(self $pair): bool;
}
