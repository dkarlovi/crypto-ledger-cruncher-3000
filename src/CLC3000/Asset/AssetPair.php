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

namespace Dkarlovi\CLC3000\Asset;

use Dkarlovi\CLC3000\Asset;
use Dkarlovi\CLC3000\AssetPair as AssetPairInterface;

/**
 * Class AssetPair.
 */
class AssetPair implements AssetPairInterface
{
    /**
     * @var Asset
     */
    private $from;

    /**
     * @var Asset
     */
    private $to;

    /**
     * @param Asset $from
     * @param Asset $to
     */
    public function __construct(Asset $from, Asset $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @return Asset
     */
    public function getFrom(): Asset
    {
        return $this->from;
    }

    /**
     * @return Asset
     */
    public function getTo(): Asset
    {
        return $this->to;
    }

    /**
     * @param AssetPairInterface $pair
     *
     * @return bool
     */
    public function equals(AssetPairInterface $pair): bool
    {
        return $pair->getFrom()->equals($this->getFrom()) && $pair->getTo()->equals($this->getTo());
    }
}
