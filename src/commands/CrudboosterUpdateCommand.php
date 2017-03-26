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

class CrudboosterUpdateCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'crudbooster:update';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'CRUDBooster Update Command';

	/**
	 * Execute the console command. 
	 *
	 * @return mixed
	 */
	public function handle()
	{

		$this->info($this->description);
		$this->info('---');
		$this->info('Thank you for choose the CRUDBooster');
		$this->info('---');

		$this->info('Publishing files...');
		$this->callSilent('vendor:publish');		
		$this->callSilent('vendor:publish',['--tag'=>'cb_migration','--force'=>true]);
		$this->callSilent('vendor:publish',['--tag'=>'cb_lfm','--force'=>true]);	
		$this->callSilent('vendor:publish',['--tag'=>'cb_localization','--force'=>true]);
		
		$this->info('Update database...');
		$this->callSilent('migrate',['--seed'=>true]);
		$this->call('config:cache');		
		
		$this->info('Update CRUDBooster Done !');
	}

}
