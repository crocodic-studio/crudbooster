<?php namespace crocodicstudio\crudbooster\controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminProfileController extends BaseController {


    public function getIndex() {
        $data = [];
        $data['page_title'] = cbLang("profile");
        return view(getThemePath('profile'),$data);
    }

    public function postUpdate() {
        validator(request()->all(),[
            'name'=>'required|max:255|min:3',
            'email'=>'required|email',
            'photo'=>'image',
            'password'=>'confirmed'
        ]);

        try {
            $data = [];
            $data['name'] = request('name');
            $data['email'] = request('email');
            if(request('password')) {
                $data['password'] = Hash::make(request('password'));
            }
            if(request()->hasFile('photo')) {
                $data['photo'] = cb()->uploadFile('photo', true, 200, 200);
            }

            DB::table("users")->where("id", auth()->id())->update($data);
        }catch (\Exception $e) {
            Log::error($e);
            return cb()->redirectBack(cbLang("something_went_wrong"),"warning");
        }

        return cb()->redirectBack("The profile data has been updated!","success");
    }

}
