<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 8/26/2019
 * Time: 4:50 PM
 */

namespace crocodicstudio\crudbooster\helpers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class ComposerHelper
{
    private static function findComposer()
    {
        if (file_exists(getcwd().'/composer.phar')) {
            return '"'.PHP_BINARY.'" '.getcwd().'/composer.phar';
        }

        return 'composer';
    }

    private static function findPHP() {
        return PHP_BINARY;
    }

    public static function dumpAutoLoad()
    {
        $composer = self::findComposer();
        $process = new Process($composer.' dump-autoload');
        $process->setTimeout(0);
        $process->setWorkingDirectory(base_path())->run();
    }

    public static function composerRemove($package) {
        $composer = self::findComposer();

        // Composer require
        Log::debug($composer.' remove '.$package);
        $process = new Process($composer.' remove '.$package);
        $process->setTimeout(0);
        $process->setWorkingDirectory(base_path())->run();
    }

    public static function composerRequire($package, $serviceProvider) {
        $composer = self::findComposer();
        $php = self::findPHP();

        // Composer require
        $process = new Process($composer.' require '.$package);
        $process->setTimeout(0);
        $process->setInput("Y");
        $process->setWorkingDirectory(base_path())->run();

        Artisan::call("vendor:publish",["--provider"=>$serviceProvider]);

        return $process->getOutput();
    }

}