<?php

namespace crocodicstudio\crudbooster\Modules\ApiGeneratorModule;

use crocodicstudio\crudbooster\controllers\CBController;
use crocodicstudio\crudbooster\Modules\ModuleGenerator\ControllerGenerator\FieldDetector;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use CRUDbooster;

class AdminApiGeneratorController extends CBController
{
    public function cbInit()
    {
        $this->table = 'cms_apicustom';
        $this->primary_key = "id";
        $this->title_field = "nama";
        $this->button_show = false;
        $this->button_new = false;
        $this->button_delete = false;
        $this->button_add = false;
        $this->button_import = false;
        $this->button_export = false;
    }

    function getIndex()
    {
        $this->cbLoader();

        $data = [];

        $data['page_title'] = 'API Generator';
        $data['apis'] = $this->table()->orderby('nama', 'asc')->get();

        return view('CbApiGen::api_documentation', $data);
    }

    function apiDocumentation()
    {
        $this->cbLoader();
        $data = [];

        $data['apis'] = $this->table()->orderby('nama', 'asc')->get();

        return view('CbApiGen::api_documentation_public', $data);
    }

    function getDownloadPostman()
    {
        $this->cbLoader();
        $data = [];
        $data['variables'] = [];
        $data['info'] = [
            'name' => CRUDBooster::getSetting('appname').' - API',
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
            }else{
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
            'Content-Disposition' => 'attachment; filename='.CRUDBooster::getSetting('appname').' - API For POSTMAN.json',
        ]);
    }

    public function getScreetKey()
    {
        $this->cbLoader();
        $data['page_title'] = 'API Generator';
        $data['apikeys'] = DB::table('cms_apikey')->get();

        return view('CbApiGen::api_key', $data);
    }

    public function getGenerator()
    {
        $this->cbLoader();

        $data['page_title'] = 'API Generator';
        $data['tables'] = \CB::listCbTables();

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

        $data['tables'] = \CB::listCbTables();

        return view('CbApiGen::api_generator', $data);
    }

    function getGenerateScreetKey()
    {
        $this->cbLoader();
        //Generate a random string.
        $token = openssl_random_pseudo_bytes(16);

        //Convert the binary data into hexadecimal representation.
        $token = bin2hex($token);

        $id = DB::table('cms_apikey')->insertGetId([
                'screetkey' => $token,
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 'active',
                'hit' => 0,
            ]);

        $response = [];
        $response['key'] = $token;
        $response['id'] = $id;

        return response()->json($response);
    }

    public function getStatusApikey()
    {
        CRUDBooster::valid(['id', 'status'], 'view');

        $id = request('id');
        $status = (request('status') == 1) ? "active" : "non active";

        DB::table('cms_apikey')->where('id', $id)->update(['status' => $status]);

        return CRUDBooster::backWithMsg('You have been update api key status !');
    }

    public function getDeleteApiKey()
    {
        $id = request('id');
        if (DB::table('cms_apikey')->where('id', $id)->delete()) {
            return response()->json(['status' => 1]);
        }

        return response()->json(['status' => 0]);
    }

    function getColumnTable($table, $type = 'list')
    {
        $this->cbLoader();
        $result = [];

        $cols = CRUDBooster::getTableColumns($table);

        $except = ['created_at', 'deleted_at', 'updated_at'];

        $result = $cols;
        $new_result = [];
        foreach ($result as $ro) {

            if (in_array($ro, $except)) {
                continue;
            }

            $type_field = CRUDBooster::getFieldType($table, $ro);

            $type_field = FieldDetector::isEmail($ro) ? "email" : $type_field;
            $type_field = FieldDetector::isImage($ro) ? "image" : $type_field;
            $type_field = FieldDetector::isPassword($ro) ? "password" : $type_field;

            $type_field = (substr($ro, -3) == '_id') ? "integer" : $type_field;
            $type_field = (substr($ro, 0, 3) == 'id_') ? "integer" : $type_field;

            $new_result[] = ['name' => $ro, 'type' => $type_field];

            if (!in_array($type, ['list', 'detail']) || substr($ro, 0, 3) !== 'id_') {
                continue;
            }
            $table2 = substr($ro, 3);
            foreach (DB::getSchemaBuilder()->getColumnListing($table2) as $col) {
                if (FieldDetector::isExceptional($col)) {
                    continue;
                }
                if (substr($col, 0, 3) == 'id_') {
                    continue;
                }

                $type_field = CRUDBooster::getFieldType($table2, $col);
                $col = str_replace("_$table2", "", $col);
                $new_result[] = ['name' => $table2.'_'.$col, 'type' => $type_field];
            }
        }

        return response()->json($new_result);
    }

    function postSaveApiCustom()
    {
        $this->cbLoader();
        $posts = Request::all();

        $_data = [];

        $_data['nama'] = g('nama');
        $_data['tabel'] = $posts['tabel'];
        $_data['aksi'] = $posts['aksi'];
        $_data['permalink'] = g('permalink');
        $_data['method_type'] = g('method_type');

        $json = $this->json(g('params_name'), g('params_type'), g('params_config'), g('params_required'), g('params_used'));

        $_data['parameters'] = serialize(array_filter($json));

        $_data['sql_where'] = g('sql_where');

        $json = $this->json2(g('responses_name'), g('responses_type'), g('responses_subquery'), g('responses_used'));
        $json = array_filter($json);
        $_data['responses'] = serialize($json);
        $_data['keterangan'] = g('keterangan');

        $this->saveToDB($_data);

        return redirect(CRUDBooster::mainpath())->with(['message' => 'Yeay, your api has been saved successfully !', 'message_type' => 'success']);
    }

    function getDeleteApi($id)
    {
        $this->cbLoader();
        $row = $this->findRow($id)->first();
        $this->findRow($id)->delete();

        $controllername = ucwords(str_replace('_', ' ', $row->permalink));
        $controllername = str_replace(' ', '', $controllername);
        @unlink(base_path(controllers_dir()."Api".$controllername."Controller.php"));

        return response()->json(['status' => 1]);
    }

    /**
     * @param $params_name
     * @param $params_type
     * @param $params_config
     * @param $params_required
     * @param $params_used
     * @param $json
     * @return array
     */
    private function json($params_name, $params_type, $params_config, $params_required, $params_used, $json)
    {
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
     * @param $responses_name
     * @param $responses_type
     * @param $responses_subquery
     * @param $responses_used
     * @return array
     */
    private function json2($responses_name, $responses_type, $responses_subquery, $responses_used)
    {
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
        $path = base_path(controllers_dir());
        file_put_contents($path.'Api'.$controller_name.'Controller.php', $php);
    }
}
