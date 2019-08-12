<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 8/9/2019
 * Time: 3:00 PM
 */

namespace crocodicstudio\crudbooster\commands;


use Illuminate\Console\Command;
use Illuminate\Support\Str;

class DeveloperCommandService
{

    private $command;
    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    public function create()
    {
        if($this->command->)
        if($this->command->option("password") == "AUTO") {
            $password = Str::random(16);
        }else{
            $password = $this->command->option("password");
        }

        if($this->command->option("username") == "AUTO") {
            $username = Str::random(5);
        }else{
            $username = $this->command->option("username");
        }

        $developerPath = Str::random();
        $developerURL = url("developer/".$developerPath."/login");

        putSetting("developer_username", $username);
        putSetting("developer_password", $password);
        putSetting("developer_path", $developerPath);

        $this->command->info("::Developer Credential::\nURL: $developerURL\nUsername: $username\nPassword: $password");
    }
}