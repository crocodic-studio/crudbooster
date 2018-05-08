<?php

namespace crocodicstudio\crudbooster\commands;

use crocodicstudio\crudbooster\CBCoreModule\Installer\CbInstaller;
use crocodicstudio\crudbooster\CBCoreModule\Installer\ConsolePrinter;
use crocodicstudio\crudbooster\CBCoreModule\Installer\RequirementChecker;
use Illuminate\Console\Command;

class CrudboosterInstallationCommand extends Command
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
        /* Removing the default user and password reset, it makes you ambigous when using CRUDBooster */
        $installer = new CbInstaller($this);

        $installer->removeDefaultMigrations();

        $installer->createVendorAtPublic();

        $installer->symlinkForUpload();

        $installer->symlinkForAsset();

        if ($this->confirm('Do you have setting the database configuration at .env ?')) {
            $installer->installCrudbooster();
        } else {
            $this->info('Setup Aborted !');
            $this->info('Please setting the database configuration for first !');
        }

        $printer->printFooter();
    }
}
