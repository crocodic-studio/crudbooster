<?php namespace crocodicstudio\crudbooster\commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use DB;
use Cache;
use Request;
use CRUDBooster;
use App;

class CrudboosterInstallationCommand extends Command {

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
		
		$this->info($this->description);
		$this->info('---');
		$this->info('Thank you for choosing the CRUDBooster');
		$this->info('---');

		if($this->confirm('Do you have setting the database configuration at .env ?')) {

			$this->info('Publishing files...');
			$this->callSilent('vendor:publish');	
			$this->callSilent('vendor:publish',['--tag'=>'cb_migration','--force'=>true]);
			$this->callSilent('vendor:publish',['--tag'=>'cb_lfm','--force'=>true]);	
			$this->callSilent('vendor:publish',['--tag'=>'cb_localization','--force'=>true]);		
			
			$this->info('Migrating database...');				
			$this->call('migrate',['--seed'=>true]);				
			$this->call('config:cache');		
			$this->call('optimize');
			
			$this->info('Install CRUDBooster Is Done !');
		}else{
			$this->info('Please setting the database configuration for first !');
		}
	}

}
