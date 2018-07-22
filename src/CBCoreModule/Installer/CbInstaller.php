<?php

namespace Crocodicstudio\Crudbooster\CBCoreModule\Installer;

use Symfony\Component\Process\Process;

class CbInstaller
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

    public function createVendorAtPublic()
    {
        $this->console->info('Checking public/vendor directory...');
        if (! file_exists(public_path('vendor'))) {
            mkdir(public_path('vendor'), 0777);
        }

        if (! is_writable(public_path('vendor'))) {
            $this->console->info('Setup aborted !');
            $this->console->info('Please set public/vendor directory to writable 0777');
            exit;
        }
    }

    public function installCrudbooster()
    {
        $this->publishFiles();
        $this->composerDumpAutoload();
        $this->migrateDatabase();
        $this->seedDatabase();
        $this->console->call('config:clear');
        if (app()->version() < 5.6) {
            $this->console->call('optimize');
        }

        $this->console->info('Installing CRUDBooster Is Completed ! Thank You :)');
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    private function findComposer()
    {
        if (file_exists(getcwd().'/composer.phar')) {
            return '"'.PHP_BINARY.'" '.getcwd().'/composer.phar';
        }

        return 'composer';
    }

    private function publishFiles()
    {
        $this->console->info('Publishing CRUDBooster needs file...');
        $this->console->callSilent('vendor:publish', ['--provider' => 'Crocodicstudio\\Crudbooster\\CRUDBoosterServiceProvider', '--force' => true]);
        $this->console->callSilent('vendor:publish', ['--tag' => 'cb_migration', '--force' => true]);
        $this->console->callSilent('vendor:publish', ['--tag' => 'cb_lfm', '--force' => true]);
        $this->console->callSilent('vendor:publish', ['--tag' => 'cb_localization', '--force' => true]);
    }

    private function migrateDatabase()
    {
        $this->console->info('Migrating Database...');
        $this->console->callSilent('migrate', ['--path' => '\database\migrations\crudbooster']);
        $this->console->callSilent('migrate');
    }

    private function seedDatabase()
    {
        $this->console->info('Seeding Database...');
        $this->console->callSilent('db:seed', ['--class' => '\Crocodicstudio\Crudbooster\Modules\EmailTemplates\Seeder']);
        $this->console->callSilent('db:seed', ['--class' => '\Crocodicstudio\Crudbooster\Modules\SettingModule\Seeder']);
        $this->console->callSilent('db:seed', ['--class' => '\Crocodicstudio\Crudbooster\Modules\PrivilegeModule\Seeder']);
    }

    private function composerDumpAutoload()
    {
        $this->console->info('Dumping the autoloaded files and reloading all new files...');
        $composer = $this->findComposer();
        $process = new Process($composer.' dumpautoload');
        $process->setWorkingDirectory(base_path())->run();
    }
}