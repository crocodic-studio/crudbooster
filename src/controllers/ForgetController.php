<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 8/14/2019
 * Time: 8:45 PM
 */

namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\exceptions\CBValidationException;
use crocodicstudio\crudbooster\helpers\MailHelper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

trait ForgetController
{

    public function postForget() {
        try {
            cb()->validation(['email'=>'required|email']);

            if($user = cb()->find("users",["email"=>request("email")])) {
                $token = Str::random(6);
                $linkToken = cb()->getAdminUrl("continue-reset/".$token);
                Cache::put("forget_".$token, $user->id, now()->addHours(12));

                Log::info("Someone trying reset password:\n
                        Time: ".date("Y-m-d H:i:s")."\n
                        IP: ".request()->ip()."\n
                        User Agent: ".request()->userAgent()."\n
                        Email Entered: ".request("email"));

                $mail = new MailHelper();
                $mail->to($user->email);
                $mail->sender(getSetting("forget_email_sender","noreply@".$_SERVER['SERVER_NAME']),cb()->getAppName());
                $mail->content("Please Confirm Your Forgot Password","
                    Hi $user->name,<br/><br>
                    Someone with the detail bellow: <br/>
                    Time = ".now()->format("Y-m-d H:i:s")."<br/>
                    IP Address = ".request()->ip()."<br/>
                    Browser = ".request()->userAgent()."<br/>
                    <p>
                        Trying to reset your password. If this is you, please click the following url to continue: <br>
                        <a href='$linkToken' target='_blank'>$linkToken</a>
                    </p>
                    <br><br>
                    Thank you <br/>
                    ".cb()->getAppName()."
                ");
                $mail->send();

            } else {
                return cb()->redirectBack("Your email is not registered");
            }

        }catch (CBValidationException $e) {
            return cb()->redirectBack($e->getMessage());
        } catch (\Exception $e) {
            Log::error($e);
            return cb()->redirectBack(cbLang("something_went_wrong"));
        }

        return cb()->redirectBack("We've sent you an email instruction. Please follow the instruction inside the email","success");
    }

    public function getContinueReset($token) {
        if(Cache::has("forget_".$token)) {
            $id = Cache::get("forget_".$token);
            $newPassword = Str::random(6);
            cb()->update("users", $id, ["password"=>Hash::make($newPassword)]);

            $user = cb()->find("users",$id);

            $mail = new MailHelper();
            $mail->to($user->email);
            $mail->sender(getSetting("forget_email_sender","noreply@".$_SERVER['SERVER_NAME']),cb()->getAppName());
            $mail->content("This Is Your New Password","
                    Hi $user->name,<br/><br>
                    Thank you for confirming the request new password. Here is your new password: <br>
                    <h2>$newPassword</h2>
                    
                    <br><br>
                    Thank you <br/>
                    ".cb()->getAppName()."
                ");
            $mail->send();

            return cb()->redirect(cb()->getAdminUrl("login"),"We've sent you new password email. Please check at your mail inbox or spambox","success");
        } else {
            return cb()->redirect(cb()->getAdminUrl("login"),"It looks like the url has been expired!");
        }
    }

}