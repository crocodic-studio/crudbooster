#! /usr/bin/env php
<?php

/*
 * This file is part of Class Preloader.
 *
 * (c) Graham Campbell <graham@cachethq.io>
 * (c) Michael Dowling <mtdowling@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (file_exists($autoloadPath = __DIR__.'/../../autoload.php')) {
    require_once $autoloadPath;
} else {
    require_once __DIR__.'/vendor/autoload.php';
}

$application = new ClassPreloader\Application();
$application->run();
