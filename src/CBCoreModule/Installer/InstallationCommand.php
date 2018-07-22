<?php

namespace Crocodicstudio\Crudbooster\commands;

use Crocodicstudio\Crudbooster\CBCoreModule\Installer\CbInstaller;
use Crocodicstudio\Crudbooster\CBCoreModule\Installer\ConsolePrinter;
use Crocodicstudio\Crudbooster\CBCoreModule\Installer\RequirementChecker;
use Illuminate\Console\Command;

class InstallationCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'crudbooster:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CRUDBooster Installation Command';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $printer = new ConsolePrinter($this);

        $printer->printHeader();

        if (! ((new RequirementChecker($this))->check())) {
            $this->info('Sorry unfortunately your system is not meet with our requirements !');
            $printer->printFooter(false);
            $this->info('--');

            return;
        }
        $this->info('Installing: ');
        $installer = new CbInstaller($this);
        $installer->createVendorAtPublic();
        if ($this->confirm('Do you have setting the database configuration at .env ?')) {
            $installer->installCrudbooster();
        } else {
            $this->info('Setup Aborted !');
            $this->info('Please setting the database configuration for first !');
        }

        $printer->printFooter();
    }
}
