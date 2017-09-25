<?php namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\controllers\Controller;
use crocodicstudio\crudbooster\controllers\Forms\SettingsForm;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\PDF;
use Illuminate\Support\Facades\Excel;
use CRUDBooster;

class AdminSettingsController extends CBController
{
    public function cbInit()
    {
        $this->table = 'cms_settings';
        $this->title_field = "name";
        $this->index_orderby = ['name' => 'asc'];
        $this->button_delete = true;
        $this->button_show = false;
        $this->button_cancel = false;
        $this->button_import = false;
        $this->button_export = false;

        $this->col = [];

        $this->form = SettingsForm::makeForm(Request::get('group_setting', 'General Setting'));
    }

    function getShow()
    {
        $this->cbLoader();

        $this->allowOnlySuperAdmin();

        $data['page_title'] = urldecode(Request::get('group'));

        return view('crudbooster::setting', $data);
    }

    function hook_before_edit(&$posdata, $id)
    {
        $this->return_url = CRUDBooster::mainpath("show")."?group=".$posdata['group_setting'];
    }

    function getDeleteFileSetting()
    {
        $id = g('id');
        $row = CRUDBooster::first('cms_settings', $id);
        if (Storage::exists($row->content)) {
            Storage::delete($row->content);
        }
        $this->table()->where('id', $id)->update(['content' => null]);
        CRUDBooster::redirect(Request::server('HTTP_REFERER'), trans('alert_delete_data_success'), 'success');
    }

    function postSaveSetting()
    {

        $this->allowOnlySuperAdmin();

        $group = Request::get('group_setting');
        $setting = $this->table()->where('group_setting', $group)->get();
        foreach ($setting as $set) {

            $name = $set->name;

            $content = Request::get($set->name);

            if (Request::hasFile($name)) {
                $content = $this->uploadFile($set);
            }

            $this->table()->where('name', $set->name)->update(['content' => $content]);

            Cache::forget('setting_'.$set->name);
        }

        return CRUDBooster::backWithMsg('Your setting has been saved !');
    }

    function hook_before_add(&$arr)
    {
        $arr['name'] = str_slug($arr['label'], '_');
        $this->return_url = CRUDBooster::mainpath("show")."?group=".$arr['group_setting'];
    }

    function hook_after_edit($id)
    {
        $row = $this->table()->where($this->primary_key, $id)->first();

        /* REMOVE CACHE */
        Cache::forget('setting_'.$row->name);
    }

    /**
     * @param $name
     * @param $set
     */
    private function validateFileType($set)
    {
        $name = $set->name;
        $rules = [$name => 'image|max:10000'];

        if ($set->content_input_type !== 'upload_image') {
            $rules = [$name => 'mimes:doc,docx,xls,xlsx,ppt,pptx,pdf,zip,rar|max:20000'];
        }

        CRUDBooster::valid($rules, 'view');
    }

    private function allowOnlySuperAdmin()
    {
        if (! CRUDBooster::isSuperadmin()) {
            CRUDBooster::insertLog(trans("crudbooster.log_try_view", ['name' => 'Setting', 'module' => 'Setting']));
            CRUDBooster::denyAccess();
        }
    }

    /**
     * @param $set
     * @param $name
     * @return string
     */
    private function uploadFile($set)
    {
        $this->validateFileType($set);

        $file = Request::file($set->name);
        $ext = $file->getClientOriginalExtension();

        //Create Directory Monthly
        Storage::makeDirectory(date('Y-m'));

        //Move file to storage
        $filename = md5(str_random(5)).'.'.$ext;
        if ($file->move(storage_path('app'.DIRECTORY_SEPARATOR.date('Y-m')), $filename)) {
            $content = 'uploads/'.date('Y-m').'/'.$filename;
        }

        return $content;
    }
}
