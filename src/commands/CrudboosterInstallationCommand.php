<?php namespace crocodicstudio\crudbooster\commands;

use App;
use Cache;
use DB;
use Illuminate\Console\Command;
use Request;
use Symfony\Component\Process\Process;

class CrudboosterInstallationCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'crudbooster:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CRUDBooster Installation Command';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $printer = new ConsolePrinter($this);

        $printer->printHeader();

        if (! ((new RequirementChecker($this))->check())) {
            $this->info('Sorry unfortunately your system is not meet with our requirements !');
            $printer->printFooter(false);
            $this->info('--');

            return;
        }

        $this->info('Installing: ');
        /* Removing the default user and password reset, it makes you ambigous when using CRUDBooster */
        $this->removeDefaultMigrations();

        //Create vendor folder at public
        $this->createVendorAtPublic();

        //Create symlink for uploads path
        $this->symlinkForUpload();

        //Crate symlink for assets
        $this->symlinkForAsset();

        if ($this->confirm('Do you have setting the database configuration at .env ?')) {
            $this->installCrudbooster();
        } else {
            $this->info('Setup Aborted !');
            $this->info('Please setting the database configuration for first !');
        }

        $printer->printFooter();
    }

    private function removeDefaultMigrations()
    {
        $this->info('I remove some default migration files from laravel...');
        @unlink(base_path('database/migrations/2014_10_12_000000_create_users_table.php'));
        @unlink(base_path('database/migrations/2014_10_12_100000_create_password_resets_table.php'));
    }

    private function createVendorAtPublic()
    {
        $this->info('Checking public/vendor directory...');
        if (! file_exists(public_path('vendor'))) {
            mkdir(public_path('vendor'), 0777);
        }

        if (! is_writable(public_path('vendor'))) {
            $this->info('Setup aborted !');
            $this->info('Please set public/vendor directory to writable 0777');
            exit;
        }
    }

    private function symlinkForUpload()
    {
        $this->info('Checking public/uploads symlink...');
        if (! file_exists(public_path('uploads'))) {
            $this->info('Creating public/uploads symlink...');
            app('files')->link(storage_path('app'), public_path('uploads'));

            return;
        }
        $uploadPath = public_path('uploads');
        $this->info('Upload Path: '.$uploadPath);
        if (realpath($uploadPath) == $uploadPath) {
            $this->info('Remove the existing uploads dir, and create a symlink for it...');
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

    private function symlinkForAsset()
    {
        $this->info('Checking public/vendor/crudbooster symlink...');

        $vendorPath = public_path('vendor'.DIRECTORY_SEPARATOR.'crudbooster');

        if (! file_exists($vendorPath)) {
            $this->info('Creating public/vendor/crudbooster symlink...');
            app('files')->link(__DIR__.'/../assets', public_path('vendor/crudbooster'));

            return;
        }

        $this->info('Vendor Path: '.$vendorPath);

        if (realpath($vendorPath) == $vendorPath) {
            $this->info('Removing public/vendor/crudbooster dir, instead of creating a symlink...');
            $this->rrmdir($vendorPath);
            app('files')->link(__DIR__.'/../assets', $vendorPath);
        }
    }

    private function installCrudbooster()
    {
        $this->info('Publishing CRUDBooster needs file...');
        $this->callSilent('vendor:publish', ['--provider' => 'crocodicstudio\\crudbooster\\CRUDBoosterServiceProvider', '--force' => true]);
        $this->callSilent('vendor:publish', ['--tag' => 'cb_migration', '--force' => true]);
        $this->callSilent('vendor:publish', ['--tag' => 'cb_lfm', '--force' => true]);
        $this->callSilent('vendor:publish', ['--tag' => 'cb_localization', '--force' => true]);

        $this->info('Dumping the autoloaded files and reloading all new files...');
        $composer = $this->findComposer();
        $process = new Process($composer.' dumpautoload');
        $process->setWorkingDirectory(base_path())->run();

        $this->info('Migrating database...');
        $this->call('migrate', ['--path' => '\database\migrations\crudbooster']);

        if (! class_exists('CBSeeder')) {
            require_once __DIR__.'/../database/seeds/CBSeeder.php';
        }
        $this->callSilent('db:seed', ['--class' => 'CBSeeder']);
        $this->call('config:clear');
        if (app()->version() < 5.6) {
            $this->call('optimize');
        }

        $this->info('Installing CRUDBooster Is Completed ! Thank You :)');
    }

    /*
    * http://stackoverflow.com/questions/3338123/how-do-i-recursively-delete-a-directory-and-its-entire-contents-files-sub-dir
    */

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer()
    {
        if (file_exists(getcwd().'/composer.phar')) {
            return '"'.PHP_BINARY.'" '.getcwd().'/composer.phar';
        }

        return 'composer';
    }
}
