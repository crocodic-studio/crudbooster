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

        try {
            if(@$this->command->option("password") == "AUTO" || !@$this->command->option("password")) {
                $password = Str::random(16);
            }else{
                $password = $this->command->option("password");
            }

            if(@$this->command->option("username") == "AUTO" || !@$this->command->option("username")) {
                $username = Str::random(5);
            }else{
                $username = $this->command->option("username");
            }
        } catch (\Exception $e) {
            $password = Str::random(16);
            $username = Str::random(5);
        }

        $developerPath = Str::random();
        $developerURL = url("developer/".$developerPath."/login");

        putSetting("developer_username", $username);
        putSetting("developer_password", $password);
        putSetting("developer_path", $developerPath);

        $this->command->info("::Developer Credential::\nURL: $developerURL\nUsername: $username\nPassword: $password");
    }
}