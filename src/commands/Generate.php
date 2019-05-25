<?php namespace crocodicstudio\crudbooster\commands;

use App;
use Cache;
use crocodicstudio\crudbooster\helpers\ModuleGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class Generate extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'crudbooster:make {--module= : The table that want to generate the module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CRUDBooster Make a Module';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info($this->description);
        $table = $this->option("module");
        (new ModuleGenerator($table))->make();
        $this->info("New module from table ".$table." has been created!");
    }
}
