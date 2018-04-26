<?php namespace crocodicstudio\crudbooster\commands;

use App;
use Cache;
use CRUDBooster;
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

        $this->header();

        $this->checkRequirements();

        $this->info('Installing: ');
        /* Removing the default user and password reset, it makes you ambigous when using CRUDBooster */
        $this->info('I remove some default migration files from laravel...');
        if (file_exists(base_path('database/migrations/2014_10_12_000000_create_users_table.php'))) {
            @unlink(base_path('database/migrations/2014_10_12_000000_create_users_table.php'));
        }
        if (file_exists(base_path('database/migrations/2014_10_12_100000_create_password_resets_table.php'))) {
            @unlink(base_path('database/migrations/2014_10_12_100000_create_password_resets_table.php'));
        }

        if ($this->confirm('Do you have setting the database configuration at .env ?')) {

            if (! file_exists(public_path('vendor'))) {
                mkdir(public_path('vendor'), 0777);
            }

            $this->info('Publishing CRUDBooster needs file...');
            $this->call('vendor:publish', ['--provider' => 'crocodicstudio\crudbooster\CRUDBoosterServiceProvider']);
            $this->call('vendor:publish', ['--provider' => 'Unisharp\Laravelfilemanager\LaravelFilemanagerServiceProvider']);
            $this->call('vendor:publish', ['--tag' => 'cb_migration', '--force' => true]);
            $this->call('vendor:publish', ['--tag' => 'cb_localization', '--force' => true]);

            $configLFM = config_path('lfm.php');
            $configLFM = file_get_contents($configLFM);
            $configLFMModified = str_replace("['web','auth']", "['web','\crocodicstudio\crudbooster\middlewares\CBBackend']", $configLFM);
            $configLFMModified = str_replace('Unisharp\Laravelfilemanager\Handlers\ConfigHandler::class', 'function() {return Session::get("admin_id");}', $configLFMModified);
            $configLFMModified = str_replace('auth()->user()->id', 'Session::get("admin_id")', $configLFMModified);
            $configLFMModified = str_replace("'alphanumeric_filename' => false", "'alphanumeric_filename' => true", $configLFMModified);
            $configLFMModified = str_replace("'alphanumeric_directory' => false", "'alphanumeric_directory' => true", $configLFMModified);
            $configLFMModified = str_replace("'alphanumeric_directory' => false", "'alphanumeric_directory' => true", $configLFMModified);
            $configLFMModified = str_replace("'base_directory' => 'public'", "'base_directory' => 'storage/app'", $configLFMModified);
            $configLFMModified = str_replace("'images_folder_name' => 'photos'", "'images_folder_name' => 'uploads'", $configLFMModified);
            $configLFMModified = str_replace("'files_folder_name'  => 'files'", "'files_folder_name'  => 'uploads'", $configLFMModified);
            file_put_contents(config_path('lfm.php'), $configLFMModified);

            $this->info('Dumping the autoloaded files and reloading all new files...');
            $composer = $this->findComposer();
            $process = new Process($composer.' dumpautoload');
            $process->setWorkingDirectory(base_path())->run();

            $this->info('Migrating database...');
            $this->call('migrate');

            if (! class_exists('CBSeeder')) {
                require_once __DIR__.'/../database/seeds/CBSeeder.php';
            }
            $this->call('db:seed', ['--class' => 'CBSeeder']);
            $this->call('config:clear');
            if (app()->version() < 5.6) {
                $this->call('optimize');
            }

            $this->info('Installing CRUDBooster Is Completed ! Thank You :)');
        } else {
            $this->info('Setup Aborted !');
            $this->info('Please setting the database configuration for first !');
        }

        $this->footer();
    }

    private function header()
    {
        $this->info("
#     __________  __  ______  ____                   __           
#    / ____/ __ \/ / / / __ \/ __ )____  ____  _____/ /____  _____
#   / /   / /_/ / / / / / / / __  / __ \/ __ \/ ___/ __/ _ \/ ___/
#  / /___/ _, _/ /_/ / /_/ / /_/ / /_/ / /_/ (__  ) /_/  __/ /    
#  \____/_/ |_|\____/_____/_____/\____/\____/____/\__/\___/_/     
#                                                                                                                       
			");
        $this->info('--------- :===: Thanks for choosing CRUDBooster :==: ---------------');
        $this->info('====================================================================');
    }

    private function checkRequirements()
    {
        $this->info('System Requirements Checking:');
        $system_failed = 0;
        $laravel = app();

        if ($laravel::VERSION >= 5.3) {
            $this->info('Laravel Version (>= 5.3.*): [Good]');
        } else {
            $this->info('Laravel Version (>= 5.3.*): [Bad]');
            $system_failed++;
        }

        if (version_compare(phpversion(), '5.6.0', '>=')) {
            $this->info('PHP Version (>= 5.6.*): [Good]');
        } else {
            $this->info('PHP Version (>= 5.6.*): [Bad] Yours: '.phpversion());
            $system_failed++;
        }

        if (extension_loaded('mbstring')) {
            $this->info('Mbstring extension: [Good]');
        } else {
            $this->info('Mbstring extension: [Bad]');
            $system_failed++;
        }

        if (extension_loaded('openssl')) {
            $this->info('OpenSSL extension: [Good]');
        } else {
            $this->info('OpenSSL extension: [Bad]');
            $system_failed++;
        }

        if (extension_loaded('pdo')) {
            $this->info('PDO extension: [Good]');
        } else {
            $this->info('PDO extension: [Bad]');
            $system_failed++;
        }

        if (extension_loaded('tokenizer')) {
            $this->info('Tokenizer extension: [Good]');
        } else {
            $this->info('Tokenizer extension: [Bad]');
            $system_failed++;
        }

        if (extension_loaded('xml')) {
            $this->info('XML extension: [Good]');
        } else {
            $this->info('XML extension: [Bad]');
            $system_failed++;
        }

        if (extension_loaded('gd')) {
            $this->info('GD extension: [Good]');
        } else {
            $this->info('GD extension: [Bad]');
            $system_failed++;
        }

        if (extension_loaded('fileinfo')) {
            $this->info('PHP Fileinfo extension: [Good]');
        } else {
            $this->info('PHP Fileinfo extension: [Bad]');
            $system_failed++;
        }

        if (is_writable(base_path('public'))) {
            $this->info('public dir is writable: [Good]');
        } else {
            $this->info('public dir is writable: [Bad]');
            $system_failed++;
        }

        if ($system_failed != 0) {
            $this->info('Sorry unfortunately your system is not meet with our requirements !');
            $this->footer(false);
        }
        $this->info('--');
    }

    private function footer($success = true)
    {
        $this->info('--');
        $this->info('Homepage : http://www.crudbooster.com');
        $this->info('Github : https://github.com/crocodic-studio/crudbooster');
        $this->info('Documentation : https://github.com/crocodic-studio/crudbooster/blob/master/docs/en/index.md');
        $this->info('====================================================================');
        if ($success == true) {
            $this->info('------------------- :===: Completed !! :===: ------------------------');
        } else {
            $this->info('------------------- :===: Failed !!    :===: ------------------------');
        }
        exit;
    }

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
