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

namespace ClassPreloader;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Finder\Finder;

/**
 * This is the application class.
 *
 * This is sets everything up for the CLI.
 */
class Application extends BaseApplication
{
    /**
     * Create a new application.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct('Class Preloader', '1.4');

        // Create a finder to find each non-abstract command in the filesystem
        $finder = new Finder();
        $finder->files()
            ->in(__DIR__.'/Command')
            ->notName('Abstract*')
            ->name('*.php');

        // Add each command to the CLI
        foreach ($finder as $file) {
            $filename = str_replace('\\', '/', $file->getRealpath());
            $pos = strripos($filename, '/ClassPreloader/') + strlen('/ClassPreloader/src/');
            $class = __NAMESPACE__.'\\'
                .substr(str_replace('/', '\\', substr($filename, $pos)), 0, -4);
            $this->add(new $class());
        }
    }
}
