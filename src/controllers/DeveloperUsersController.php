<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/25/2019
 * Time: 9:28 PM
 */

namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\exceptions\CBValidationException;
use crocodicstudio\crudbooster\helpers\ModuleGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DeveloperUsersController extends Controller
{

    private $view = "crudbooster::dev_layouts.modules.users";

    public function __construct()
    {
        view()->share(['page_title'=>'User Management']);
    }


    public function getIndex() {
        $data = [];
        $data['result'] = DB::table("users")
            ->join("cb_roles","cb_roles.id","=","cb_roles_id")
            ->select("users.*","cb_roles.name as cb_roles_name")
            ->get();
        return view($this->view.'.index',$data);
    }

    public function getAdd() {
        $data = [];
        $data['roles'] = DB::table("cb_roles")->get();
        return view($this->view.'.add', $data);
    }

    public function postAddSave() {
        try {
            cb()->validation(['name', 'email','password','cb_roles_id']);

            $user = [];
            $user['name'] = request('name');
            $user['email'] = request('email');
            $user['password'] = Hash::make(request('password'));
            $user['cb_roles_id'] = request('cb_roles_id');
            DB::table('users')->insert($user);

            return cb()->redirect(route("DeveloperUsersControllerGetIndex"),"New user has been created!","success");

        } catch (CBValidationException $e) {
            return cb()->redirectBack($e->getMessage());
        }
    }

    public function getEdit($id) {
        $data = [];
        $data['row'] = cb()->find("users", $id);
        $data['roles'] = DB::table("cb_roles")->get();
        return view($this->view.".edit", $data);
    }

    public function postEditSave($id) {
        try {
            cb()->validation(['name', 'email','cb_roles_id']);

            $user = [];
            $user['name'] = request('name');
            $user['email'] = request('email');
            if(request('password')) $user['password'] = Hash::make(request('password'));
            $user['cb_roles_id'] = request('cb_roles_id');
            DB::table('users')->where('id',$id)->update($user);

            return cb()->redirect(route("DeveloperUsersControllerGetIndex"),"The user has been updated!","success");

        } catch (CBValidationException $e) {
            return cb()->redirectBack($e->getMessage());
        }
    }

    public function getDelete($id) {
        DB::table("users")->where("id",$id)->delete();
        return cb()->redirectBack("The user has been deleted!","success");
    }

}