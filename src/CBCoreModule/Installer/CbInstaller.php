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

    public function symlinkForUpload()
    {
        $this->console->info('Checking public/uploads symlink...');
        if (! file_exists(public_path('uploads'))) {
            $this->console->info('Creating public/uploads symlink...');
            app('files')->link(storage_path('app'), public_path('uploads'));

            return;
        }
        $uploadPath = public_path('uploads');
        $this->console->info('Upload Path: '.$uploadPath);
        if (realpath($uploadPath) == $uploadPath) {
            $this->console->info('Remove the existing uploads dir, and create a symlink for it...');
            $this->rrmdir(public_path('uploads'));
            app('files')->link(storage_path('app'), public_path('uploads'));
        }
    }

    private function rrmdir($dir)
    {
        if (! is_dir($dir)) {
            return;
        }
        foreach (scandir($dir) as $object) {
            if (in_array($object, ['.', '..'])) {
                continue;
            }

            $objPath = $dir."/".$object;

            if (is_dir($objPath)) {
                $this->rrmdir($objPath);
            } else {
                unlink($objPath);
            }
        }
        rmdir($dir);
    }

    public function symlinkForAsset()
    {
        $this->console->info('Checking public/vendor/crudbooster symlink...');

        $ds = DIRECTORY_SEPARATOR;
        $vendorPath = public_path('vendor'.$ds.'crudbooster');
        $assetPath = base_path('vendor'.$ds.'crocodicstudio'.$ds.'crudbooster'.$ds.'src'.$ds.'assets');

        if (! file_exists($vendorPath)) {
            $this->console->info('Creating public/vendor/crudbooster symlink...');
            app('files')->link($assetPath, public_path('vendor/crudbooster'));

            return;
        }

        $this->console->info('Vendor Path: '.$vendorPath);

        if (realpath($vendorPath) == $vendorPath) {
            $this->console->info('Removing public/vendor/crudbooster dir, instead of creating a symlink...');
            $this->rrmdir($vendorPath);
            app('files')->link($assetPath, $vendorPath);
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
     *
     * http://stackoverflow.com/questions/3338123/how-do-i-recursively-delete-a-directory-and-its-entire-contents-files-sub-dir
     */

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