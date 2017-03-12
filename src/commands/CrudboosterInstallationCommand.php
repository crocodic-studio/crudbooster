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
		$this->info('Thank you for choose the CRUDBooster');
		$this->info('---');

		$this->info('Publishing files...');
		$this->callSilent('vendor:publish');	
		$this->callSilent('vendor:publish',['--tag'=>'cb_migration','--force'=>'default']);
		$this->callSilent('vendor:publish',['--tag'=>'cb_lfm','--force'=>'default']);	
		$this->callSilent('vendor:publish',['--tag'=>'cb_localization','--force'=>'default']);	

		$this->info('Setting The Database Configuration : ');
		$db_name = $this->ask('DATABASE NAME');
		$db_username = $this->ask('USERNAME');
		$db_password = $this->ask('PASSWORD',false);
		if($db_password===FALSE) {			
			$db_password = '';
		}

		copy(base_path('.env.example'),base_path('.env'));

		file_put_contents(App::environmentFilePath(), str_replace(	        
	        'DB_DATABASE=homestead',
	        'DB_DATABASE='.$db_name,
	        file_get_contents(App::environmentFilePath())
	    ));

	    file_put_contents(App::environmentFilePath(), str_replace(	        
	        'DB_USERNAME=homestead',
	        'DB_USERNAME='.$db_username,
	        file_get_contents(App::environmentFilePath())
	    ));
	    
	    file_put_contents(App::environmentFilePath(), str_replace(	        
	        'DB_PASSWORD=secret',
	        'DB_PASSWORD='.$db_password,
	        file_get_contents(App::environmentFilePath())
	    ));

		
		$this->info('Migrating database...');
		$this->callSilent('migrate',['--seed'=>'default']);

		//Generate Key Env
		$this->callSilent('key:generate'); 
		
		$this->info('Install CRUDBooster Done !');
	}

}
