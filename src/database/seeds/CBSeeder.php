<?php

use Illuminate\Database\Seeder;

class CBSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Please wait updating the data...');
        $this->call('PrivilegeSeeder');
        $this->call('SettingsSeeder');
        $this->call('EmailTemplates');
        $this->command->info('Updating the data completed !');
    }
}

class EmailTemplates extends Seeder
{
    public function run()
    {
        \crocodicstudio\crudbooster\Modules\EmailTemplates\Seeder::run();
    }
}

class SettingsSeeder extends Seeder
{
    public function run()
    {
        \crocodicstudio\crudbooster\Modules\SettingModule\Seeder::run();
    }
}

class PrivilegeSeeder extends Seeder
{
    public function run()
    {
        \crocodicstudio\crudbooster\Modules\PrivilegeModule\Seeder::run();
    }
}