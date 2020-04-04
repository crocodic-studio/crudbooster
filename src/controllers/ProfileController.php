<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/3/2020
 * Time: 10:04 PM
 */

namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\models\CmsUsers;
use Illuminate\Support\Facades\Hash;
use crocodicstudio\crudbooster\exceptions\CBValidationException;

trait ProfileController
{
    public function getIndex()
    {
        return view('crudbooster::profile',['page_title'=>trans('crudbooster.label_button_profile')]);
    }

    public function postSave() {
        try {
            $id_column = config('crudbooster.LOGIN_ID_COLUMN.column','email');
            $pass_column = config('crudbooster.LOGIN_PASS_COLUMN.column','password');

            cb()->validation(['name',$id_column,$pass_column]);

            $cms_user = cb()->auth()->user();
            $cms_user->updated_at = date_now();
            $cms_user->name = request('name');
            $cms_user->{$id_column} = request($id_column);
            if(request($pass_column)) {
                $cms_user->password = Hash::make(request($pass_column));
            }
            if(request()->hasFile('photo')) {
                $cms_user->photo = cb()->uploadFile('photo',true);
            }
            $cms_user->save();

            return redirect_back(trans('crudbooster.alert_update_data_success'),'success');

        } catch (CBValidationException $e) {
            return redirect_back($e->getMessage(),'warning');
        }
    }

}