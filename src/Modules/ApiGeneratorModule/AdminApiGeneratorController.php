<?php

namespace crocodicstudio\crudbooster\Modules\ApiGeneratorModule;

use crocodicstudio\crudbooster\controllers\CBController;
use crocodicstudio\crudbooster\helpers\CRUDBooster;

class AdminApiGeneratorController extends CBController
{
    public function cbInit()
    {
        $this->table = 'cms_apicustom';
        $this->primaryKey = "id";
        $this->titleField = "name";
        $this->buttonShow = false;
        $this->deleteBtn = false;
        $this->buttonAdd = false;
        $this->buttonImport = false;
        $this->buttonExport = false;
    }

    public function getIndex()
    {
        $this->cbLoader();

        $data = [];

        $data['page_title'] = 'API Generator';
        $data['apis'] = $this->table()->orderby('name', 'asc')->get();

        return view('CbApiGen::api_documentation', $data);
    }

    public function apiDocumentation()
    {
        $this->cbLoader();
        $apis = $this->table()->orderby('name', 'asc')->get();
        return view('CbApiGen::api_documentation_public', compact('apis'));
    }

    public function getGenerator()
    {
        $this->cbLoader();
        $data = [
            'page_title' => 'API Generator',
            'tables' => CRUDBooster::listCbTables(),
        ];

        return view('CbApiGen::api_generator', $data);
    }

    public function getEditApi($id)
    {
        $this->cbLoader();

        $row = $this->findRow($id)->first();
        $data = [
            'row' => $row,
            'parameters' => json_encode(unserialize($row->parameters)),
            'responses' => json_encode(unserialize($row->responses)),
            'page_title' => 'API Generator',
            'tables' => CRUDBooster::listCbTables(),
        ];

        return view('CbApiGen::api_generator', $data);
    }

    public function postSaveApiCustom()
    {
        $this->cbLoader();
        $posts = request()->all();

        $_data = [
            'name' => g('name'),
            'tabel' => $posts['tabel'],
            'aksi' => $posts['aksi'],
            'permalink' => g('permalink'),
            'method_type' => g('method_type'),
            'sql_where' => g('sql_where'),
            'keterangan' => g('keterangan'),
            'parameters' => serialize(array_filter($this->json())),
            'responses' => serialize(array_filter($this->json2())),
        ];

        $this->saveToDB($_data);

        return redirect(CRUDBooster::mainpath())->with(['message' => 'Yeay, your api has been saved successfully !', 'message_type' => 'success']);
    }

    public function getDeleteApi($id)
    {
        $this->cbLoader();
        $row = $this->findRow($id)->first();
        $this->findRow($id)->delete();

        $controllername = ucwords(str_replace('_', ' ', $row->permalink));
        $controllername = str_replace(' ', '', $controllername);
        @unlink((controllers_dir()."Api".$controllername."Controller.php"));

        return response()->json(['status' => 1]);
    }

    /**
     * @return array
     */
    private function json()
    {
        $params_name = g('params_name');
        $params_type = g('params_type');
        $params_config = g('params_config');
        $params_required = g('params_required');
        $params_used = g('params_used');

        $json = [];
        for ($i = 0, $_count = count($params_name); $i <= $_count; $i++) {
            if (! $params_name[$i]) {
                continue;
            }
            $json[] = [
                'name' => $params_name[$i],
                'type' => $params_type[$i],
                'config' => $params_config[$i],
                'required' => $params_required[$i],
                'used' => $params_used[$i],
            ];
        }

        return $json;
    }

    /**
     * @return array
     */
    private function json2()
    {
        $responses_name = g('responses_name');
        $responses_type = g('responses_type');
        $responses_subquery = g('responses_subquery');
        $responses_used = g('responses_used');

        $json = [];
        for ($i = 0, $_count = count($responses_name); $i <= $_count; $i++) {
            if (! $responses_name[$i]) {
                continue;
            }
            $json[] = [
                'name' => $responses_name[$i],
                'type' => $responses_type[$i],
                'subquery' => $responses_subquery[$i],
                'used' => $responses_used[$i],
            ];
        }

        return $json;
    }

    /**
     * @param $a
     */
    private function saveToDB($a)
    {
        if (request('id')) {
            return $this->findRow(g('id'))->update($a);
        }

        $controllerName = ucwords(str_replace('_', ' ', $a['permalink']));
        $controllerName = str_replace(' ', '', $controllerName);
        $this->generateAPI($controllerName, $a['tabel'], $a['permalink'], $a['method_type']);

        return $this->table()->insert($a);
    }

    private function generateAPI($controller_name, $table_name, $permalink, $method_type = 'post')
    {
        $php = '<?php '.view('CbApiGen::api_stub', compact('controller_name', 'table_name', 'permalink', 'method_type'))->render();
        file_put_contents(controllers_dir().'Api'.$controller_name.'Controller.php', $php);
    }
}
