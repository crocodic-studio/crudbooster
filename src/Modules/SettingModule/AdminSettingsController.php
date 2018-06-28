<?php

namespace Crocodicstudio\Crudbooster\Modules\SettingModule;

use Crocodicstudio\Crudbooster\controllers\CBController;
use Crocodicstudio\Crudbooster\Helpers\CbValidator;
use Crocodicstudio\Crudbooster\Helpers\CRUDBooster;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

class AdminSettingsController extends CBController
{
    /**
     * AdminSettingsController constructor.
     */
    public function __construct()
    {
        $this->table = 'cms_settings';
        $this->titleField = "name";
    }

    public function cbInit()
    {
        $this->orderby = ['name' => 'asc'];

        $this->setButtons();

        $this->col = [];

        $this->form = SettingsForm::makeForm(request('group_setting', 'General Setting'));
    }

    public function getShow()
    {
        CRUDBooster::allowOnlySuperAdmin();

        $this->cbLoader();

        $data = ['page_title' => urldecode(request('group'))];

        return view('CbSettings::setting', $data);
    }

    public function hookBeforeEdit($postData, $id)
    {
        $this->return_url = CRUDBooster::mainpath("show")."?group=".$postData['group_setting'];
        return $postData;
    }

    public function getDeleteFileSetting()
    {
        $this->genericLoader();
        $id = request('id');
        $content = CRUDBooster::first($this->table, $id)->content;

        Storage::delete($content);

        $this->findRow($id)->update(['content' => null]);

        backWithMsg(cbTrans('alert_delete_data_success'));
    }

    public function postSaveSetting()
    {
        CRUDBooster::allowOnlySuperAdmin();

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

        backWithMsg(cbTrans('Update_Setting'));
    }

    public function hookBeforeAdd($arr)
    {
        $arr['name'] = str_slug($arr['label'], '_');
        $this->return_url = CRUDBooster::mainpath("show")."?group=".$arr['group_setting'];
        return $arr;
    }

    public function hookAfterEdit($id)
    {
        $row = $this->findRow($id)->first();

        /* REMOVE CACHE */
        Cache::forget('setting_'.$row->name);
    }

    /**
     * @param $set
     */
    private function validateFileType($set)
    {
        $name = $set->name;
        $rules = [$name => 'image|max:10000'];

        if ($set->content_input_type !== 'upload_image') {
            $rules = [$name => 'mimes:doc,docx,xls,xlsx,ppt,pptx,pdf,zip,rar|max:20000'];
        }

        CbValidator::valid($rules, 'view');
    }

    /**
     * @param $set
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
            return 'uploads/'.$month.'/'.$filename;
        }
    }

    private function setButtons()
    {
        $this->buttonShow = false;
        $this->buttonCancel = false;
        $this->buttonImport = false;
        $this->buttonExport = false;
    }
}
