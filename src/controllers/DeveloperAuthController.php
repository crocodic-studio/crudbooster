<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 8/13/2019
 * Time: 7:08 PM
 */

namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\exceptions\CBValidationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

trait DeveloperAuthController
{
    public function getLoginDeveloper() {
        return view('crudbooster::dev_layouts.login');
    }

    public function postLoginDeveloper() {
        try{
            cb()->validation([
                'username'=>'required',
                'password'=>'required'
            ]);

            if(request('username') == getSetting('developer_username')
                && request('password') == getSetting("developer_password")) {

                session(['developer'=>getSetting('developer_username')]);

                return redirect(cb()->getDeveloperUrl());

            }else{
                return cb()->redirectBack( cbLang("password_and_username_is_wrong"));
            }

        }catch (CBValidationException $e) {
            return cb()->redirect(cb()->getLoginUrl(),$e->getMessage(),'warning');
        }
    }

    public function getLogoutDeveloper() {
        session()->forget("developer");

        return redirect(cb()->getAdminUrl("login"));
    }

}