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
    protected $signature = 'make:module {table : The table that want to generate the module}';

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
        $table = $this->argument("table");
        (new ModuleGenerator($table))->make();
        $this->info("New module created!");
    }
}
