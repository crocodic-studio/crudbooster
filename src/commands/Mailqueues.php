<?php namespace crocodicstudio\crudbooster\commands;

use crocodicstudio\crudbooster\helpers\Mailer;
use Illuminate\Console\Command;
use DB;
use Cache;
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
        $now = YmdHis();

        $this->comment('Mail Queues Started '.$now);

        $queues = DB::table('cms_email_queues')->where('send_at', '<=', $now)->take($limit_an_hour)->get();

        $this->comment('Total Queues : '.count($queues));

        Cache::increment('total_email_sent', count($queues));
        Cache::put('last_email_sent', YmdHis());

        foreach ($queues as $q) {
            if (filter_var($q->email_recipient, FILTER_VALIDATE_EMAIL) !== false) {
                (new Mailer())->sendEmailQueue($q);
                $this->comment('Email send -> '.$q->subject);
            }
            DB::table('mailqueues')->where('id', $q->id)->delete();
        }
    }
}
