<?php

namespace Crocodicstudio\Crudbooster\CBCoreModule\Installer;

use Illuminate\Foundation\Application;

class RequirementChecker
{

    private $console;

    /**
     * ConsolePrinter constructor.
     *
     * @param $console
     */
    public function __construct($console)
    {
        $this->console = $console;
    }

    private $requirements = true;

    public function check()
    {
        $this->console->info('System Requirements Checking:');
        $this->checkLaravelVersion();
        $this->checkPHPversion();

        $extensions = ['mbstring', 'openssl', 'pdo', 'tokenizer', 'xml', 'gd', 'fileinfo',];
        array_walk($extensions, function ($ext){
            $this->chechExtension($ext);
        });

        $this->checkWritableFolders();

        return $this->requirements;
    }

    private function checkPHPversion()
    {
        if (version_compare(phpversion(), '7.0', '>=')) {
            $this->console->info('PHP Version (>= 7.0): [Good]');
            return;
        }
        $this->console->info('PHP Version (>= 7.0): [Bad] Yours: '.phpversion());
        $this->requirements = false;
    }

    private function checkLaravelVersion()
    {
        if (Application::VERSION >= 5.4) {
            $this->console->info('Laravel Version (>= 5.4.*): [Good]');
            return;
        }

        $this->console->info('Laravel Version (>= 5.4.*): [Bad]');
        $this->requirements = false;

    }

    private function checkWritableFolders()
    {
        if (is_writable(base_path('public'))) {
            $this->console->info('public dir is writable: [Good]');
            return true;
        }
        $this->console->info('public dir is writable: [Bad]');
        return $this->requirements = false;
    }

    /**
     * @param $extension
     * @return boolean
     */
    private function chechExtension($extension)
    {
        if (! extension_loaded($extension)) {
            $this->console->info($extension.' extension: [Bad]');
            return $this->requirements = false;
        }

        $this->console->info($extension.' extension: [Good]');
        return true;
    }
}