<?php namespace crocodicstudio\crudbooster\commands;

use App;
use Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class MigrateData extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'crudbooster:seed {--generate : Add this option to generate the seeder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CRUDBooster: Generate CB Data Seeder';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        if($this->option("generate")) {
            $this->info("== Generating Seeder ==");
            $this->generateSeeder();
            $this->info("== Finish ==");
        }else{
            $this->info("== Importing the seeder ==");
            $this->call("db:seed",["--class"=>"CbMigrationSeeder"]);
            $this->info("== Finish ==");
        }

    }

    private function generateSeeder() {
        set_time_limit(120);
        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
        $php_string = "";
        $additional_tables = cbConfig("ADDITIONAL_DATA_MIGRATION");

        foreach($tables as $table) {
            if(substr($table,0,3) == "cb_" || in_array($table, $additional_tables)) {
                $this->info("Create seeder for table : ".$table);
                $rows = DB::table($table)->get();
                $data = [];
                foreach($rows as $i=>$row) {
                    $data[$i] = [];
                    foreach($row as $key=>$val) {
                        $data[$i][$key] = $val;
                    }
                }
                if(count($data)!=0) {
                    $php_string .= 'DB::table(\''.$table.'\')->delete();'."\n\t\t\t";
                    $php_string .= 'DB::table(\''.$table.'\')->insert('.min_var_export($data).');'."\n\t\t\t";
                }
            }
        }
        $seederFileTemplate = '
<?php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class CbMigrationSeeder extends Seeder
{
    public function run()
    {
        $this->command->info(\'Please wait updating the data...\');                
        $this->call(\'CbMigrationData\');        
        $this->command->info(\'Updating the data completed !\');
    }
}
class CbMigrationData extends Seeder {
    public function run() {        
    	'.$php_string.'
    }
}
	';
        file_put_contents(base_path('database/seeds/CbMigrationSeeder.php'), $seederFileTemplate);

        $composer_path = '';
        if (file_exists(getcwd().'/composer.phar')) {
            $composer_path = '"'.PHP_BINARY.'" '.getcwd().'/composer.phar';
        }else{
            $composer_path = 'composer';
        }

        $this->info('Dumping auto loads new file seeder !');
        $process = new Process($composer_path.' dump-autoload');
        $process->setWorkingDirectory(base_path())->run();

    }
}
