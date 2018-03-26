<?php

namespace crocodicstudio\crudbooster\commands;

class RequirementChecker
{

    private $requirements = true;

    function check()
    {
        $this->info('System Requirements Checking:');
        $this->checkLaravelVersion();
        $this->checkPHPversion();
        $this->chechExtension('mbstring');
        $this->chechExtension('openssl');
        $this->chechExtension('pdo');
        $this->chechExtension('tokenizer');
        $this->chechExtension('xml');
        $this->chechExtension('gd');
        $this->chechExtension('fileinfo');
        $this->checkWritableFolders();

        return $this->requirements;
    }

    private function checkPHPversion()
    {
        if (version_compare(phpversion(), '5.6.0', '>=')) {
            $this->info('PHP Version (>= 5.6.*): [Good]');
        } else {
            $this->info('PHP Version (>= 5.6.*): [Bad] Yours: '.phpversion());
            $this->requirements = false;
        }
    }

    private function checkLaravelVersion()
    {
        $laravel = app();
        if ($laravel::VERSION >= 5.3) {
            $this->info('Laravel Version (>= 5.3.*): [Good]');
            return true;
        }
        $this->info('Laravel Version (>= 5.3.*): [Bad]');
        return $this->requirements = false;

    }

    private function checkWritableFolders()
    {
        if (is_writable(base_path('public'))) {
            $this->info('public dir is writable: [Good]');
            return true;
        }
        $this->info('public dir is writable: [Bad]');
        return $this->requirements = false;

    }

    /**
     * @param $extension
     * @return true
     */
    private function chechExtension($extension)
    {
        if (! extension_loaded($extension)) {
            $this->info($extension.' extension: [Bad]');
            return $this->requirements = false;
        }

        $this->info($extension.' extension: [Good]');
        return true;
    }
}