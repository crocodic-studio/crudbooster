<?php

namespace crocodicstudio\crudbooster\Modules\SettingModule;

use crocodicstudio\crudbooster\controllers\CBController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Cache;
use CRUDBooster;

class AdminSettingsController extends CBController
{
    /**
     * AdminSettingsController constructor.
     */
    public function __construct()
    {
        $this->table = 'cms_settings';
        $this->title_field = "name";
    }

    public function cbInit()
    {
        $this->index_orderby = ['name' => 'asc'];

        $this->setButtons();

        $this->col = [];

        $this->form = SettingsForm::makeForm(request('group_setting', 'General Setting'));
    }

    function getShow()
    {
        $this->cbLoader();

        $this->allowOnlySuperAdmin();

        $data['page_title'] = urldecode(request('group'));

        return view('CbSettings::setting', $data);
    }

    function hookBeforeEdit(&$posdata, $id)
    {
        $this->return_url = CRUDBooster::mainpath("show")."?group=".$posdata['group_setting'];
    }

    function getDeleteFileSetting()
    {
        $id = request('id');
        $content = CRUDBooster::first($this->table, $id)->content;

        Storage::delete($content);

        $this->table()->where('id', $id)->update(['content' => null]);

        CRUDBooster::redirect(Request::server('HTTP_REFERER'), trans('alert_delete_data_success'), 'success');
    }

    function postSaveSetting()
    {
        $this->allowOnlySuperAdmin();

        $group = request('group_setting');

        $settings = $this->table()->where('group_setting', $group)->get();

        foreach ($settings as $setting) {

            $name = $setting->name;

            $content = request($name);
            if (Request::hasFile($name)) {
                $content = $this->uploadFile($setting);
            }

            $this->table()->where('name', $name)->update(['content' => $content]);

            Cache::forget('setting_'.$name);
        }

        return CRUDBooster::backWithMsg('Your setting has been saved !');
    }

    function hookBeforeAdd(&$arr)
    {
        $arr['name'] = str_slug($arr['label'], '_');
        $this->return_url = CRUDBooster::mainpath("show")."?group=".$arr['group_setting'];
    }

    function hookAfterEdit($id)
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
        $month = date('Y-m');

        $file = Request::file($set->name);
        //Create Directory Monthly
        Storage::makeDirectory($month);

        //Move file to storage
        $filename = md5(str_random(5)).'.'.$file->getClientOriginalExtension();
        if ($file->move(storage_path('app'.DIRECTORY_SEPARATOR.$month), $filename)) {
            $content = 'uploads/'.$month.'/'.$filename;
        }

        return $content;
    }

    private function setButtons()
    {
        $this->button_delete = true;
        $this->button_show = false;
        $this->button_cancel = false;
        $this->button_import = false;
        $this->button_export = false;
    }
}
