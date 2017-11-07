<?php

namespace crocodicstudio\crudbooster\Modules\StatisticModule;

use crocodicstudio\crudbooster\controllers\CBController;
use Illuminate\Support\Facades\DB;
use CRUDBooster;

class AdminStatisticBuilderController extends CBController
{
    public function cbInit()
    {
        $this->table = "cms_statistics";
        $this->primary_key = "id";
        $this->title_field = "name";
        $this->limit = 20;
        $this->orderby = "id,desc";
        $this->global_privilege = false;

        $this->setButtons();

        $this->makeColumns();

        $this->form = StatisticForm::makeForm();

        $this->addaction = [];
        $this->addaction[] = ['label' => 'Builder', 'url' => CRUDBooster::mainpath('builder').'/[id]', 'icon' => 'fa fa-wrench'];
    }

    public function getShowDashboard()
    {
        $this->cbLoader();
        $m = CRUDBooster::sidebarDashboard();
        $m->path = str_replace("statistic_builder/show/", "", $m->path);
        if ($m->type != 'Statistic') {
            redirect('/');
        }
        $row = CRUDBooster::first($this->table, ['slug' => $m->path]);

        $id_cms_statistics = $row->id;
        $page_title = $row->name;

        return view('CbStatistics::show', compact('page_title', 'id_cms_statistics'));
    }

    public function getShow($slug)
    {
        $this->cbLoader();
        $row = CRUDBooster::first($this->table, ['slug' => $slug]);
        $id_cms_statistics = $row->id;
        $page_title = $row->name;

        return view('CbStatistics::show', compact('page_title', 'id_cms_statistics'));
    }

    public function getDashboard()
    {
        $this->cbLoader();

        $menus = DB::table('cms_menus')->where('is_dashboard', 1)->where('type', 'Statistic')->first();
        $slug = str_replace(["statistic-builder/show/", "statistic_builder/show/"], "", $menus->path);

        $row = CRUDBooster::first($this->table, ['slug' => $slug]);
        $id_cms_statistics = $row->id;
        $page_title = $row->name;

        return view('CbStatistics::show', compact('page_title', 'id_cms_statistics'));
    }

    public function getBuilder($id_cms_statistics)
    {
        $this->cbLoader();
        $this->allowOnlySuperAdmin('Builder');

        $page_title = 'Statistic Builder';

        return view('CbStatistics::builder', compact('page_title', 'id_cms_statistics'));
    }

    public function getListComponent($id_cms_statistics, $area_name)
    {
        $rows = DB::table('cms_statistic_components')->where('id_cms_statistics', $id_cms_statistics)->where('area_name', $area_name)->orderby('sorting', 'asc')->get();

        return response()->json(['components' => $rows]);
    }

    public function getViewComponent($componentID)
    {
        $component = CRUDBooster::first('cms_statistic_components', ['componentID' => $componentID]);

        $command = 'layout';
        $layout = view('CbStatistics::components.'.$component->component_name, compact('command', 'componentID'))->render();

        $component_name = $component->component_name;
        $area_name = $component->area_name;
        $config = json_decode($component->config);
        if (! $config) {
            return response()->json(compact('componentID', 'layout'));
        }
        foreach ($config as $key => $value) {
            if (! $value) {
                continue;
            }
            $command = 'showFunction';
            $value = view('CbStatistics::components.'.$component_name, compact('command', 'value', 'key', 'config', 'componentID'))->render();
            $layout = str_replace('['.$key.']', $value, $layout);
        }

        return response()->json(compact('componentID', 'layout'));
    }

    public function postAddComponent()
    {
        $this->cbLoader();
        $component_name = request('component_name');
        $componentID = md5(time());

        $command = 'layout';

        $data = [
            'id_cms_statistics' => request('id_cms_statistics'),
            'componentID' => $componentID,
            'component_name' => $component_name,
            'area_name' => request('area'),
            'sorting' => request('sorting'),
            'name' => 'Untitled',
        ];
        CRUDBooster::insert('cms_statistic_components', $data);

        $layout = view('CbStatistics::components.'.$component_name, compact('command', 'componentID'))->render();

        return response()->json(compact('layout', 'componentID'));
    }

    public function postUpdateAreaComponent()
    {
        DB::table('cms_statistic_components')->where('componentID', request('componentid'))->update([
                'sorting' => request('sorting'),
                'area_name' => request('areaname'),
            ]);

        return response()->json(['status' => true]);
    }

    public function getEditComponent($componentID)
    {
        $this->cbLoader();
        $this->allowOnlySuperAdmin('Edit Component');

        $component_row = CRUDBooster::first('cms_statistic_components', ['componentID' => $componentID]);

        $config = json_decode($component_row->config);

        $command = 'configuration';

        return view('CbStatistics::components.'.$component_row->component_name, compact('command', 'componentID', 'config'));
    }

    public function postSaveComponent()
    {
        DB::table('cms_statistic_components')->where('componentID', request('componentid'))->update([
                'name' => request('name'),
                'config' => json_encode(request('config')),
            ]);

        return response()->json(['status' => true]);
    }

    public function getDeleteComponent($id)
    {
        $this->allowOnlySuperAdmin('Delete Component');

        DB::table('cms_statistic_components')->where('componentID', $id)->delete();

        return response()->json(['status' => true]);
    }

    public function hookBeforeAdd(&$arr)
    {
        //Your code here
        $arr['slug'] = str_slug($arr['name']);
    }

    public function hookBeforeEdit(&$postdata, $id)
    {
        $postdata['slug'] = str_slug($postdata['name']);
    }

    private function allowOnlySuperAdmin($name)
    {
        if (CRUDBooster::isSuperadmin()) {
            return;
        }
        CRUDBooster::insertLog(trans("crudbooster.log_try_view", ['name' => $name, 'module' => 'Statistic']));
        CRUDBooster::denyAccess();
    }

    private function makeColumns()
    {
        $this->col = [];
        $this->col[] = ["label" => "Name", "name" => "name"];
    }

    private function setButtons()
    {
        $this->button_table_action = true;
        $this->button_action_style = "button_icon_text";
        $this->button_add = true;
        $this->button_delete = true;
        $this->button_edit = true;
        $this->button_detail = false;
        $this->button_show = true;
        $this->button_filter = false;
        $this->button_export = false;
        $this->button_import = false;
    }
}
