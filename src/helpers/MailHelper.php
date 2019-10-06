<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 3/13/2019
 * Time: 12:51 AM
 */

namespace crocodicstudio\crudbooster\helpers;

use Illuminate\Support\Facades\Mail;

class MailHelper
{

    private $content;
    private $sender_email;
    private $sender_name;
    private $to_email;
    private $cc_email;
    private $attachments;
    private $subject;

    public function sender($email, $name) {
        $this->sender_email = $email;
        $this->sender_name = $name;
    }

    public function subject($subject) {
        $this->subject = $subject;
    }

    public function content($content) {
        $this->content = $content;
    }

    public function to($email, $cc_email = null) {
        $this->to_email = $email;
        $this->cc_email = $cc_email;
    }

    public function addAttachment($url) {
        $this->attachments[] = $url;
    }

    public function send() {
        Mail::send("crudbooster::emails.blank", ['content' => $this->content], function ($message) {
            $message->priority(1);
            $message->to($this->to_email);
            $message->from($this->sender_email, $this->sender_name);

            if (isset($this->cc_email)) {
                $message->cc($this->cc_email);
            }

            if (isset($this->attachments) && count($this->attachments)) {
                foreach ($this->attachments as $attachment) {
                    $message->attach($attachment);
                }
            }

            $message->subject($this->subject);
        });
    }
}