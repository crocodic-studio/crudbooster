<?php

namespace crocodicstudio\crudbooster\helpers;

class Mailer
{
    private $attachments;

    private $reciever;



    public function send($config)
    {
        $this->setConfigs();

        $this->reciever = $config['to'];
        $template = $config['template'];

        $template = CRUDBooster::first('cms_email_templates', ['slug' => $template]);
        $html = $template->content;
        foreach ($config['data'] as $key => $val) {
            $html = str_replace('['.$key.']', $val, $html);
            $template->subject = str_replace('['.$key.']', $val, $template->subject);
        }
        $subject = $template->subject;
        $this->attachments = ($config['attachments']) ?: [];

        if ($config['send_at'] != null) {
            return $this->putInQueue($config, $template, $subject, $html);
        }

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
     * @param $config
     * @param $template
     * @param $subject
     * @param $html

     * @return bool
     */
    private function putInQueue($config, $template, $subject, $html)
    {
        $queue = [
            'send_at' => $config['send_at'],
            'email_recipient' => $this->reciever,
            'email_from_email' => $template->from_email ?: SettingRepo::getSetting('email_sender'),
            'email_from_name' => $template->from_name ?: SettingRepo::getSetting('appname'),
            'email_cc_email' => $template->cc_email,
            'email_subject' => $subject,
            'email_content' => $html,
            'email_attachments' => serialize($this->attachments),
            'is_sent' => 0,
        ];

        DB::table('cms_email_queues')->insert($queue);

        return true;
    }

    /**
     * @param $html
     * @param $subject
     * @param $template
     */
    private function sendMail($html, $subject, $template)
    {
        \Mail::send("crudbooster::emails.blank", ['content' => $html], function ($message) use ($subject, $template) {
            $message->priority(1);
            $message->to($this->reciever);

            if ($template->from_email) {
                $from_name = ($template->from_name) ?: SettingRepo::getSetting('appname');
                $message->from($template->from_email, $from_name);
            }

            if ($template->cc_email) {
                $message->cc($template->cc_email);
            }

            if (count($this->attachments)) {
                foreach ($this->attachments as $attachment) {
                    $message->attach($attachment);
                }
            }

            $message->subject($subject);
        });
    }

    public function sendEmailQueue($queue)
    {
        $this->setConfigs();

        $attachments = unserialize($queue->email_attachments);

        \Mail::send("crudbooster::emails.blank", ['content' => $queue->email_content], function ($message) use (
            $queue,
            $attachments
        ) {
            $message->priority(1);
            $message->to($queue->email_recipient);
            $message->from($queue->email_from_email, $queue->email_from_name);
            $message->cc($queue->email_cc_email);

            if (count($attachments)) {
                foreach ($attachments as $attachment) {
                    $message->attach($attachment);
                }
            }
            $message->subject($queue->email_subject);
        });
    }
}