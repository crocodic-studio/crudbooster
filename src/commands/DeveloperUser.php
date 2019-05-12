<?php namespace crocodicstudio\crudbooster\commands;

use App;
use Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class DeveloperUser extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'crudbooster:developer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CRUDBooster Generate Developer User';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $password = Str::random(16);
        cache()->forever("developer_password", $password);
        $this->info("Your new developer password: ".$password);
    }
}
