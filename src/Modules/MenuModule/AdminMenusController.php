<?php

namespace crocodicstudio\crudbooster\Modules\MenuModule;

use crocodicstudio\crudbooster\controllers\CBController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\PDF;
use Illuminate\Support\Facades\Excel;
use CRUDBooster;

class AdminMenusController extends CBController
{
    public function cbInit()
    {
        $this->table = "cms_menus";
        $this->primary_key = "id";
        $this->title_field = "name";
        $this->limit = 20;
        $this->orderby = ["id" => "desc"];

        $this->setButtons();

        $id = CRUDBooster::getCurrentId();
        $row = CRUDBooster::first($this->table, $id);
        $row = (Request::segment(3) == 'edit') ? $row : null;

        $this->script_js = MenuJavascript::setJs($id, $row->type);

        $this->setCols();

        list($statistic_id, $module_id) = $this->getMenuId($row);
        $this->form = MenusForm::makeForm($statistic_id, $module_id, $row);
    }

    public function getIndex()
    {
        $this->cbLoader();

        $return_url = Request::fullUrl();

        $page_title = 'Menu Management';

        return view('CbMenu::menus_management', compact('privileges', 'return_url', 'page_title'));
    }

    public function hookBeforeAdd(&$postdata)
    {
        $postdata['parent_id'] = 0;

        $postdata['path'] = $this->getMenuPath($postdata);

        unset($postdata['module_slug']);
        unset($postdata['statistic_slug']);

        if ($postdata['is_dashboard'] == 1) {
            //If set dashboard, so unset for first all dashboard
            DB::table($this->table)->where('is_dashboard', 1)->update(['is_dashboard' => 0]);
            Cache::forget('sidebarDashboard'.CRUDBooster::myPrivilegeId());
        }
    }

    public function hookBeforeEdit(&$postdata, $id)
    {
        if ($postdata['is_dashboard'] == 1) {
            //If set dashboard, so unset for first all dashboard
            DB::table($this->table)->where('is_dashboard', 1)->update(['is_dashboard' => 0]);
            Cache::forget('sidebarDashboard'.CRUDBooster::myPrivilegeId());
        }

        $postdata['path'] = $this->getMenuPath($postdata);

        unset($postdata['module_slug']);
        unset($postdata['statistic_slug']);
    }

    public function hookAfterDelete($id)
    {
        DB::table($this->table)->where('parent_id', $id)->delete();
    }

    public function postSaveMenu()
    {
        $this->cbInit();
        $isActive = Request::input('isActive');
        $post = json_decode(Request::input('menus'), true);

        foreach ($post[0] as $i => $menu) {
            $pid = $menu['id'];
            $children = $menu['children'][0] ?: [];

            foreach ($children as $index => $child) {
                $this->findRow($child['id'])->update(['sorting' => $index + 1, 'parent_id' => $pid, 'is_active' => $isActive]);
            }

            $this->findRow($pid)->update(['sorting' => $i + 1, 'parent_id' => 0, 'is_active' => $isActive]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * @param $postdata
     * @return string
     */
    private function getMenuPath($postdata)
    {
        if ($postdata['type'] == 'Statistic') {
            $stat = CRUDBooster::first('cms_statistics', ['id' => $postdata['statistic_slug']])->slug;
            return 'statistic-builder/show/'.$stat;
        }

        if ($postdata['type'] == 'Module') {
            return CRUDBooster::first('cms_moduls', ['id' => $postdata['module_slug']])->path;
        }
    }

    private function setButtons()
    {
        $this->button_table_action = true;
        $this->button_action_style = "FALSE";
        $this->button_add = false;
        $this->button_delete = true;
        $this->button_edit = true;
        $this->button_detail = true;
        $this->button_show = false;
        $this->button_filter = true;
        $this->button_export = false;
        $this->button_import = false;
    }

    /**
     * @param $row
     * @return array
     */
    private function getMenuId($row)
    {
        $id_module = $id_statistic = 0;

        if ($row->type == 'Module') {
            $id_module = DB::table('cms_moduls')->where('path', $row->path)->first()->id;
        }

        if ($row->type == 'Statistic') {
            $row->path = str_replace('statistic-builder/show/', '', $row->path);
            $id_statistic = DB::table('cms_statistics')->where('slug', $row->path)->first()->id;
        }

        return [$id_statistic, $id_module];
    }

    private function setCols()
    {
        $this->col = [];
        $this->col[] = ["label" => "Name", "name" => "name"];
        $this->col[] = ["label" => "Is Active", "name" => "is_active"];
    }
}