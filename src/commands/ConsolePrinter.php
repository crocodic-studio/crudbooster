<?php

namespace crocodicstudio\crudbooster\commands;

class ConsolePrinter
{
    function printHeader()
    {
        $this->info("

#     ______  ____    __   ______     _____                   __           
#    / ____/ / __ \  / /  / / __ \   / /__ )____  ____  _____/ /____  _____
#   / /     / /_/ / / /  / / / / /  / /__/ / __ \/ __ \/ ___/ __/ _ \/ ___/
#  / /___  / _, _ / /__/ / /_/ /  / /__/ / /_/ / /_/ (__  ) /_/  __/ /    
#  \____/ /_/ |_|  \____/_____/  /______/\____/\____/____/\__/\___/_/     
#                                                                                                                       
			");
        $this->info('--------- :===: Thanks for choosing CRUDBooster :==: ---------------');
        $this->info('====================================================================');
    }


    function printFooter($success = true)
    {
        $this->info('--');
        $this->info('Homepage : http://www.crudbooster.com');
        $this->info('Github : https://github.com/crocodic-studio/crudbooster');
        $this->info('Documentation : https://github.com/crocodic-studio/crudbooster/blob/master/docs/en/index.md');
        $this->info('====================================================================');
        if ($success == true) {
            $this->info('------------------- :===: Completed !! :===: ------------------------');
        } else {
            $this->info('------------------- :===:  Failed !!  :===: ------------------------');
        }
    }
}