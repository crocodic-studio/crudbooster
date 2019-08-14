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

trait RegisterController
{

    public function postRegister() {
        try {
            cb()->validation([
                "name"=>"required|string|max:50",
                "email"=>"required|email|max:50",
                "password"=>"required|max:25",
                "captcha"=>"required|integer"
            ]);

            if(Session::get("captcha_result") != request("captcha")) {
                return cb()->redirectBack("The captcha that you input is wrong!");
            }

            if($user = cb()->find("users",["email"=>request("email")])) {
                return cb()->redirectBack("The email you input has already exists!");
            }

            if(getSetting("register_mail_verification")) {
                Cache::put("register_".$token,[
                    "name"=>request("name"),
                    "email"=>request("email"),
                    "password"=>request("password")
                ],now()->addHours(24));

                $mail = new MailHelper();
                $mail->to(request("email"));
                $mail->sender(getSetting("register_mail_verification_sender","noreply@".$_SERVER['SERVER_NAME']),cb()->getAppName());
                $mail->content("Verify Your Registration","
                Hi ".request("name")."<br/>
                Thank you for register at ".cb()->getAppName()." to continue your registration, please click on the following link: <br/>
                <p>$linkToken</p>
                <br>
                Thank You <br>
                ".cb()->getAppName()."
                ");
                $mail->send();
            } else {
                DB::table("users")
                    ->insert([
                        "created_at"=>date("Y-m-d H:i:s"),
                        "name"=> request("name"),
                        "email"=> request("email"),
                        "password"=>Hash::make(request("password")),
                        "cb_roles_id"=> getSetting("register_as_role")
                    ]);

                return cb()->redirect(cb()->getAdminUrl("login"),"Thank you for register. Now you can login to start your session :)","success");
            }

        }catch (CBValidationException $e) {
            return cb()->redirectBack($e->getMessage());
        }

        return cb()->redirectBack("We've sent you an confirmation email. Please click the link inside the email","success");
    }

    public function postContinueRegister($token) {
        if($token = Cache::get("register_".$token)) {
            DB::table("users")
                ->insert([
                    "created_at"=>date("Y-m-d H:i:s"),
                    "name"=> $token['name'],
                    "email"=> $token['email'],
                    "password"=>Hash::make($token['password']),
                    "cb_roles_id"=> getSetting("register_as_role")
                ]);

            return cb()->redirect(cb()->getAdminUrl("login"),"Thank you for register. Now you can login to start your session :)","success");
        } else {
            return cb()->redirect(cb()->getAdminUrl("login"),"It looks like the URL has been expired!");
        }
    }

}