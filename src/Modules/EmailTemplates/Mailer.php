<?php

namespace Crocodicstudio\Crudbooster\Modules\EmailTemplates;

use Crocodicstudio\Crudbooster\Helpers\CRUDBooster;
use Crocodicstudio\Crudbooster\Modules\SettingModule\SettingRepo;
use Illuminate\Support\Facades\Config;

class Mailer
{
    private $attachments;

    private $receiver;

    public function send($config)
    {
        $this->setConfigs();

        $this->receiver = $config['to'];
        $template = $config['template'];

        $template = CRUDBooster::first('cms_email_templates', ['slug' => $template]);
        $html = $template->content;
        foreach ($config['data'] as $key => $val) {
            $html = str_replace('['.$key.']', $val, $html);
            $template->subject = str_replace('['.$key.']', $val, $template->subject);
        }
        $subject = $template->subject;
        $this->attachments = ($config['attachments']) ?: [];

        $this->sendMail($html, $subject, $template);
    }

    private function setConfigs()
    {
        Config::set('mail.driver', SettingRepo::getSetting('smtp_driver'));
        Config::set('mail.host', SettingRepo::getSetting('smtp_host'));
        Config::set('mail.port', SettingRepo::getSetting('smtp_port'));
        Config::set('mail.username', SettingRepo::getSetting('smtp_username'));
        Config::set('mail.password', SettingRepo::getSetting('smtp_password'));
    }

    /**
     * @param $html
     * @param $subject
     * @param $template
     */
    private function sendMail($html, $subject, $template)
    {
        \Mail::send("CbEmailTpl::templates.blank", ['content' => $html], function ($message) use ($subject, $template) {
            $message->priority(1);
            $message->to($this->receiver);

            if ($template->from_email) {
                $from_name = ($template->from_name) ?: SettingRepo::getSetting('appname');
                $message->from($template->from_email, $from_name);
            }

            if ($template->cc_email) {
                $message->cc($template->cc_email);
            }

            if (!empty($this->attachments)) {
                foreach ($this->attachments as $attachment) {
                    $message->attach($attachment);
                }
            }

            $message->subject($subject);
        });
    }
}