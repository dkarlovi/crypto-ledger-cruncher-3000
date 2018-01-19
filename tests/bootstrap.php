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

error_reporting(E_ALL);
if (function_exists('date_default_timezone_set') && function_exists('date_default_timezone_get')) {
    /* @noinspection PhpUsageOfSilenceOperatorInspection */
    date_default_timezone_set(@date_default_timezone_get());
}
require __DIR__.'/../src/bootstrap.php';
