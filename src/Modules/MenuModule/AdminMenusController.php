<?php

namespace crocodicstudio\crudbooster\Modules\MenuModule;

use crocodicstudio\crudbooster\controllers\CBController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use crocodicstudio\crudbooster\helpers\CRUDBooster;

class AdminMenusController extends CBController
{
    public function cbInit()
    {
        $this->table = "cms_menus";
        $this->primaryKey = "id";
        $this->titleField = "name";
        $this->limit = 20;
        $this->orderby = ["id" => "desc"];

        $this->setButtons();

        $id = CRUDBooster::getCurrentId();
        $row = CRUDBooster::first($this->table, $id);
        $row = (Request::segment(3) == 'edit') ? $row : null;

        $this->scriptJs = view('CbMenu::js', ['id' => $id, 'type' => $row->type])->render();

        $this->setCols();

        list($statisticId, $moduleId) = $this->getMenuId($row);
        $this->form = MenusForm::makeForm($statisticId, $moduleId, $row);
    }

    public function getIndex()
    {
        $this->cbLoader();

        $return_url = Request::fullUrl();

        $page_title = 'Menu Management';

        return view('CbMenu::menus_management', compact('return_url', 'page_title'));
    }

    public function hookBeforeAdd($postData)
    {
        $postData['parent_id'] = 0;

        $postData['path'] = $this->getMenuPath($postData);

        unset($postData['module_slug']);
        unset($postData['statistic_slug']);

        if ($postData['is_dashboard'] == 1) {
            //If set dashboard, so unset for first all dashboard
            $this->table()->where('is_dashboard', 1)->update(['is_dashboard' => 0]);
            Cache::forget('sidebarDashboard'.CRUDBooster::myPrivilegeId());
        }
        return $postData;
    }

    public function hookBeforeEdit($postData, $id)
    {
        if ($postData['is_dashboard'] == 1) {
            //If set dashboard, so unset for first all dashboard
            $this->table()->where('is_dashboard', 1)->update(['is_dashboard' => 0]);
            Cache::forget('sidebarDashboard'.CRUDBooster::myPrivilegeId());
        }

        $postData['path'] = $this->getMenuPath($postData);

        unset($postData['module_slug']);
        unset($postData['statistic_slug']);
        return $postData;
    }

    public function hookAfterDelete($id)
    {
        $this->table()->where('parent_id', $id)->delete();
    }

    public function postSaveMenu()
    {
        $this->cbInit();
        $isActive = request('isActive');
        $post = json_decode(request('menus'), true);

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
        $this->buttonTableAction = true;
        $this->buttonActionStyle = "FALSE";
        $this->buttonAdd = false;
        $this->deleteBtn = true;
        $this->buttonEdit = true;
        $this->buttonDetail = true;
        $this->buttonShow = false;
        $this->buttonFilter = true;
        $this->buttonExport = false;
        $this->buttonImport = false;
    }

    /**
     * @param $row
     * @return array
     */
    private function getMenuId($row)
    {
        $idModule = $idStatistic = 0;

        if ($row->type == 'Module') {
            $idModule = DB::table('cms_moduls')->where('path', $row->path)->first()->id;
        }

        if ($row->type == 'Statistic') {
            $row->path = str_replace('statistic-builder/show/', '', $row->path);
            $idStatistic = DB::table('cms_statistics')->where('slug', $row->path)->first()->id;
        }

        return [$idStatistic, $idModule];
    }

    private function setCols()
    {
        $this->col = [];
        $this->col[] = ["label" => "Name", "name" => "name"];
        $this->col[] = ["label" => "Is Active", "name" => "is_active"];
    }
}