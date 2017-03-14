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
		$default     = 'mysql';
		$db_driver   = $this->choice('DB DRIVER (DEFAULT MYSQL) ? enter to skip', ['mysql', 'pgsql','sqlsrv'], $default);
		$db_host     = $this->ask('DATABASE HOST (DEFAULT 127.0.0.1) ? enter to skip',false);
		$db_host     = ($db_host === FALSE)?'127.0.0.1':$db_host;
		$db_port     = $this->ask("DATABASE PORT (DEFAULT 3306) ? enter to skip",false);
		$db_port     = ($db_port === FALSE)?'3306':$db_port;
		$db_name     = $this->ask('DATABASE NAME ?');
		$db_username = $this->ask('USERNAME ?');
		$db_password = $this->ask('PASSWORD ?',false);
		$db_password = ($db_password === FALSE)?'':$db_password;

		copy(base_path('.env.example'),base_path('.env'));		
		$this->callSilent('key:generate');

		file_put_contents(App::environmentFilePath(), str_replace(	        
	        'DB_CONNECTION=mysql',
	        'DB_CONNECTION='.$db_driver,
	        file_get_contents(App::environmentFilePath())
	    ));

	    file_put_contents(App::environmentFilePath(), str_replace(	        
	        'DB_HOST=127.0.0.1',
	        'DB_HOST='.$db_host,
	        file_get_contents(App::environmentFilePath())
	    ));

	    file_put_contents(App::environmentFilePath(), str_replace(	        
	        'DB_PORT=3306',
	        'DB_PORT='.$db_port,
	        file_get_contents(App::environmentFilePath())
	    ));

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

	    \Config::set('database.default',$db_driver);
	    \Config::set('database.connections.'.$db_driver.'.driver',$db_driver);
	    \Config::set('database.connections.'.$db_driver.'.host',$db_host);
	    \Config::set('database.connections.'.$db_driver.'.port',$db_port);
	    \Config::set('database.connections.'.$db_driver.'.database',$db_name);
	    \Config::set('database.connections.'.$db_driver.'.username',$db_username);
	    \Config::set('database.connections.'.$db_driver.'.password',$db_password);	    
	    DB::reconnect($db_driver);

		
		$this->info('Migrating database...');		
		$this->callSilent('migrate',['--seed'=>'default']);		
		
		$this->info('Install CRUDBooster Done !');
	}

}
