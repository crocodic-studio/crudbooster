<?php

namespace crocodicstudio\crudbooster\commands;

use Illuminate\Console\Command;

class ConsolePrinter
{
    private $console;

    /**
     * ConsolePrinter constructor.
     *
     * @param $console
     */
    public function __construct(Command $console)
    {
        $this->console = $console;
    }

    public function printHeader()
    {
        $this->console->info("

#     ______  ____    __  ______     _____                   __           
#    / ____/ / __ \  / / / / __ \   /  __ )____  ____  _____/ /____  _____
#   / /     / /_/ / / / / / / / /  / /__/ / __ \/ __ \/ ___/ __/ _ \/ ___/
#  / /___  / _, _/ / /_/ / /_/ /  / /__/ / /_/ / /_/ (__  ) /_/  __/ /    
#  \____/ /_/ |_|  \____/_____/  /______/\____/\____/____/\__/\___/_/     
#                                                                                                                      
			");
        $this->console->info('--------- :===: Thanks for choosing CRUDBooster :==: ---------------');
        $this->console->info('====================================================================');
    }


    public function printFooter($success = true)
    {
        $this->console->info('--');
        $this->console->info('Homepage : http://www.crudbooster.com');
        $this->console->info('Github : https://github.com/crocodic-studio/crudbooster');
        $this->console->info('Documentation : https://github.com/crocodic-studio/crudbooster/blob/master/docs/en/index.md');
        $this->console->info('====================================================================');
        if ($success == true) {
            $this->console->info('------------------- :===: Completed !! :===: ------------------------');
        } else {
            $this->console->info('------------------- :===:  Failed !!  :===: ------------------------');
        }
    }
}