<?php namespace crocodicstudio\crudbooster\commands;

use Cache;
use crocodicstudio\crudbooster\helpers\CRUDBooster;
use DB;
use Illuminate\Console\Command;
use Request;

class Mailqueues extends Command
{
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


        $now = date('Y-m-d H:i:s');

        $this->comment('Mail Queues Started '.$now);

        $queues = db('cms_email_queues')->where('send_at', '<=', $now)->take(25)->get();

        $this->comment('Total Queues : '.count($queues));

        Cache::increment('total_email_sent', count($queues));
        Cache::put('last_email_sent', date('Y-m-d H:i:s'));

        foreach ($queues as $q) {
            if (filter_var($q->email_recipient, FILTER_VALIDATE_EMAIL) !== false) {
                CRUDBooster::sendEmailQueue($q);
                $this->comment('Email send -> '.$q->subject);
            }
            DB::table('mailqueues')->where('id', $q->id)->delete();
        }
    }
}
