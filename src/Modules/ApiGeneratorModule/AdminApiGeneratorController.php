<?php

namespace crocodicstudio\crudbooster\Modules\ApiGeneratorModule;

use crocodicstudio\crudbooster\controllers\CBController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use crocodicstudio\crudbooster\helpers\CRUDBooster;

class AdminApiGeneratorController extends CBController
{
    public function cbInit()
    {
        $this->table = 'cms_apicustom';
        $this->primaryKey = "id";
        $this->title_field = "nama";
        $this->buttonShow = false;
        $this->button_new = false;
        $this->deleteBtn = false;
        $this->buttonAdd = false;
        $this->button_import = false;
        $this->buttonExport = false;
    }

    public function getIndex()
    {
        $this->cbLoader();

        $data = [];

        $data['page_title'] = 'API Generator';
        $data['apis'] = $this->table()->orderby('nama', 'asc')->get();

        return view('CbApiGen::api_documentation', $data);
    }

    public function apiDocumentation()
    {
        $this->cbLoader();
        $data = [];

        $data['apis'] = $this->table()->orderby('nama', 'asc')->get();

        return view('CbApiGen::api_documentation_public', $data);
    }

    public function getDownloadPostman()
    {
        $this->cbLoader();
        $data = [];
        $data['variables'] = [];
        $data['info'] = [
            'name' => cbGetsetting('appname').' - API',
            '_postman_id' => "1765dd11-73d1-2978-ae11-36921dc6263d",
            'description' => '',
            'schema' => 'https://schema.getpostman.com/json/collection/v2.0.0/collection.json',
        ];
        $items = [];
        foreach ($this->table()->orderby('nama', 'asc')->get() as $api) {
            $parameters = unserialize($api->parameters);
            $formdata = [];
            $httpbuilder = [];
            if ($parameters) {
                foreach ($parameters as $p) {
                    $enabled = ($p['used'] == 0) ? false : true;
                    $name = $p['name'];
                    $httpbuilder[$name] = '';
                    if ($enabled) {
                        $formdata[] = ['key' => $name, 'value' => '', 'type' => 'text', 'enabled' => $enabled];
                    }
                }
            }

            if (strtolower($api->method_type) == 'get' && $httpbuilder) {
                $httpbuilder = "?".http_build_query($httpbuilder);
            } else {
                $httpbuilder = '';
            }

            $items[] = [
                'name' => $api->nama,
                'request' => [
                    'url' => url('api/'.$api->permalink).$httpbuilder,
                    'method' => $api->method_type ?: 'GET',
                    'header' => [],
                    'body' => [
                        'mode' => 'formdata',
                        'formdata' => $formdata,
                    ],
                    'description' => $api->keterangan,
                ],
            ];
        }
        $data['item'] = $items;

        $json = json_encode($data);

        return \Response::make($json, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename='.cbGetsetting('appname').' - API For POSTMAN.json',
        ]);
    }

    public function getGenerator()
    {
        $this->cbLoader();

        $data['page_title'] = 'API Generator';
        $data['tables'] = CRUDBooster::listCbTables();

        return view('CbApiGen::api_generator', $data);
    }

    public function getEditApi($id)
    {
        $this->cbLoader();

        $row = $this->findRow($id)->first();

        $data['row'] = $row;
        $data['parameters'] = json_encode(unserialize($row->parameters));
        $data['responses'] = json_encode(unserialize($row->responses));
        $data['page_title'] = 'API Generator';

        $data['tables'] = CRUDBooster::listCbTables();

        return view('CbApiGen::api_generator', $data);
    }

    public function postSaveApiCustom()
    {
        $this->cbLoader();
        $posts = request()->all();

        $_data = [];

        $_data['nama'] = g('nama');
        $_data['tabel'] = $posts['tabel'];
        $_data['aksi'] = $posts['aksi'];
        $_data['permalink'] = g('permalink');
        $_data['method_type'] = g('method_type');

        $json = $this->json();

        $_data['parameters'] = serialize(array_filter($json));

        $_data['sql_where'] = g('sql_where');

        $json = $this->json2();
        $json = array_filter($json);
        $_data['responses'] = serialize($json);
        $_data['keterangan'] = g('keterangan');

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
        $php = view('CbApiGen::api_stub', compact('controller_name', 'table_name', 'permalink', 'method_type'))->render();
        file_put_contents(controllers_dir().'Api'.$controller_name.'Controller.php', $php);
    }
}
