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
    protected $name = 'crudbooster:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CRUDBooster: Generate CB Data Migrate';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("== Data Migrate ==");
        $this->call("db:seed",["--class"=>"CbMigrationSeeder"]);
        $this->info("== Data Migrate Finished ==");
    }
}
