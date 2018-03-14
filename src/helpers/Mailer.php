<?php

namespace crocodicstudio\crudbooster\helpers;

class Mailer
{
    private $attachments;

    public function send($config)
    {
        $this->setConfigs();

        $to = $config['to'];
        $data = $config['data'];
        $template = $config['template'];

        $template = CRUDBooster::first('cms_email_templates', ['slug' => $template]);
        $html = $template->content;
        foreach ($data as $key => $val) {
            $html = str_replace('['.$key.']', $val, $html);
            $template->subject = str_replace('['.$key.']', $val, $template->subject);
        }
        $subject = $template->subject;
        $this->attachments = ($config['attachments']) ?: [];

        if ($config['send_at'] != null) {
            return $this->putInQueue($config, $template, $subject, $html);
        }

        $this->sendMail($html, $to, $subject, $template);
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
     * @param $config
     * @param $to
     * @param $template
     * @param $subject
     * @param $html
     * @param $attachments
     * @return bool
     */
    private function putInQueue($config, $template, $subject, $html, $attachments)
    {
        $to = $config['to'];
        $queue = [
            'send_at' => $config['send_at'],
            'email_recipient' => $to,
            'email_from_email' => $template->from_email ?: SettingRepo::getSetting('email_sender'),
            'email_from_name' => $template->from_name ?: SettingRepo::getSetting('appname'),
            'email_cc_email' => $template->cc_email,
            'email_subject' => $subject,
            'email_content' => $html,
            'email_attachments' => serialize($attachments),
            'is_sent' => 0,
        ];
        DB::table('cms_email_queues')->insert($queue);

        return true;
    }

    /**
     * @param $html
     * @param $to
     * @param $subject
     * @param $template
     * @param $attachments
     */
    private function sendMail($html, $to, $subject, $template, $attachments)
    {
        \Mail::send("crudbooster::emails.blank", ['content' => $html], function ($message) use ($to, $subject, $template, $attachments) {
            $message->priority(1);
            $message->to($to);

            if ($template->from_email) {
                $from_name = ($template->from_name) ?: SettingRepo::getSetting('appname');
                $message->from($template->from_email, $from_name);
            }

            if ($template->cc_email) {
                $message->cc($template->cc_email);
            }

            if (count($attachments)) {
                foreach ($attachments as $attachment) {
                    $message->attach($attachment);
                }
            }

            $message->subject($subject);
        });
    }
}