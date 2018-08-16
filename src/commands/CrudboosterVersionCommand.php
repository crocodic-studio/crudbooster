<?php namespace crocodicstudio\crudbooster\commands;

use App;
use Illuminate\Console\Command;

class CrudboosterVersionCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'crudbooster:version';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CRUDBooster Version Command';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Version : 5.4.15");
    }
}
