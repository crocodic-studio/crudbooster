<?php namespace crocodicstudio\crudbooster\commands;

use App;
use Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class Install extends Command
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

        if ($this->confirm('Do you have setting the database configuration at .env ?')) {

            if (! file_exists(public_path('vendor')) ) {
                mkdir(public_path('vendor'), 0777);
            }

            $this->info('Publishing CRUDBooster needs file...');
            $this->call('vendor:publish', ['--provider' => 'crocodicstudio\crudbooster\CRUDBoosterServiceProvider']);
            $this->call('vendor:publish', ['--tag' => 'cb_migration', '--force' => true]);
            $this->call('vendor:publish', ['--tag' => 'cb_localization', '--force'=> true]);

            $this->info('Dumping the autoloaded files and reloading all new files...');
            $composer = $this->findComposer();
            $process = new Process($composer.' dump-autoload');
            $process->setWorkingDirectory(base_path())->run();

            $this->info('Migrating database...');
            $this->call('migrate');

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

        if ($laravel::VERSION >= 5.6) {
            $this->info('Laravel Version (>= 5.6): [Good]');
        } else {
            $this->warn('Laravel Version (>= 5.6): [Bad]');
            $system_failed++;
        }

        if (version_compare(phpversion(), '7.1.3', '>=')) {
            $this->info('PHP Version (>= 7.*): [Good]');
        } else {
            $this->warn('PHP Version (>= 7.*): [Bad] Yours: '.phpversion());
            $system_failed++;
        }

        if (extension_loaded('mbstring')) {
            $this->info('Mbstring extension: [Good]');
        } else {
            $this->warn('Mbstring extension: [Bad]');
            $system_failed++;
        }

        if (extension_loaded('openssl')) {
            $this->info('OpenSSL extension: [Good]');
        } else {
            $this->warn('OpenSSL extension: [Bad]');
            $system_failed++;
        }

        if (extension_loaded('pdo')) {
            $this->info('PDO extension: [Good]');
        } else {
            $this->warn('PDO extension: [Bad]');
            $system_failed++;
        }

        if (extension_loaded('tokenizer')) {
            $this->info('Tokenizer extension: [Good]');
        } else {
            $this->warn('Tokenizer extension: [Bad]');
            $system_failed++;
        }

        if (extension_loaded('xml')) {
            $this->info('XML extension: [Good]');
        } else {
            $this->warn('XML extension: [Bad]');
            $system_failed++;
        }

        if (extension_loaded('gd')) {
            $this->info('GD extension: [Good]');
        } else {
            $this->warn('GD extension: [Bad]');
            $system_failed++;
        }

        if (extension_loaded('fileinfo')) {
            $this->info('PHP Fileinfo extension: [Good]');
        } else {
            $this->warn('PHP Fileinfo extension: [Bad]');
            $system_failed++;
        }

        if (is_writable(base_path('public'))) {
            $this->info('public dir is writable: [Good]');
        } else {
            $this->warn('public dir is writable: [Bad]');
            $system_failed++;
        }

        if ($system_failed != 0) {
            $this->warn('Sorry unfortunately your system is not meet with our requirements !');
            $this->footer(false);
        }
        $this->info('--');
    }

    private function footer($success = true)
    {

        $password = Str::random(16);
        cache()->forever("developer_password",$password);

        $this->info('--');
        $this->info("DEVELOPER AREA");
        $this->info("/".cbConfig("DEV_PATH")."/login");
        $this->info("username: ".cbConfig("DEV_USERNAME"));
        $this->info("Your new developer password: ".$password);
        $this->info("--");
        $this->info('Homepage : http://crudbooster.com');
        $this->info('Github : https://github.com/crocodic-studio/crudbooster');
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
