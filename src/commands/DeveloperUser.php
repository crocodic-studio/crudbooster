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
    protected $name = 'crudbooster:developer {--password=AUTO : To custom the password of developer}';

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
        if($this->option("password") == "AUTO") {
            $password = Str::random(16);
        }else{
            $password = $this->option("password");
        }

        cache()->forever("developer_password", $password);
        $this->info("Your new developer password: ".$password);
    }
}
