<?php

namespace crocodicstudio\crudbooster\commands;

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

    function check()
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
        if (version_compare(phpversion(), '5.6.0', '>=')) {
            $this->console->info('PHP Version (>= 5.6.*): [Good]');
            return;
        }
        $this->console->info('PHP Version (>= 5.6.*): [Bad] Yours: '.phpversion());
        $this->requirements = false;
    }

    private function checkLaravelVersion()
    {
        $laravel = app();
        if ($laravel::VERSION >= 5.3) {
            $this->console->info('Laravel Version (>= 5.3.*): [Good]');
            return;
        }

        $this->console->info('Laravel Version (>= 5.3.*): [Bad]');
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
     * @return true
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