<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use DB;
use Cache;
use Request;

class Mailqueues extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'mailqueues';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Run Mail Queues';

	/**
	 * Execute the console command. 
	 *
	 * @return mixed
	 */
	public function handle()
	{
		
		
		$now           = date('Y-m-d H:i:s');
		$limit_an_hour = config('crudbooster.LIMIT_EMAIL_PER_HOUR',200);

		if(Cache::has('total_email_sent')) {
			if(Cache::get('total_email_sent') >= $limit_an_hour) {

				$count_minute = diff_minute(Cache::get('last_email_sent'));

				if($count_minute >= 60) {
					Cache::put('total_email_sent',0);
				}else{
					return false;
				}				
			}
		}


		$this->comment('Mail Queues Started '.$now);

		$queues  = DB::table('cms_email_queue')->where('send_at','<=',$now)->take($limit_an_hour)->get();

		$this->comment('Total Queues : '.count($queues));

		Cache::increment('total_email_sent', count($queues));
		Cache::put('last_email_sent',date('Y-m-d H:i:s'));
		
		foreach($queues as $q) {
			if (filter_var($q->email_recipient, FILTER_VALIDATE_EMAIL) !== false) {
				$domain  = substr (Request::root(), 7);				
				$to      = $q->email_recipient;
				$subject = $q->email_subject;
				$message = $q->email_content;
				send_email($to,$subject,$message);
				$this->comment('Email send -> '.$subject);
			}
			DB::table('mailqueues')->where('id',$q->id)->delete();
		}
		
	}

}
