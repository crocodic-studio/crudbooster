<?php

namespace crocodicstudio\crudbooster\Modules\ApiGeneratorModule;

use crocodicstudio\crudbooster\controllers\CBController;
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
        $data['page_menu'] = Route::getCurrentRoute()->getActionName();
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
        $apis = $this->table()->orderby('nama', 'asc')->get();

        foreach ($apis as $a) {
            $parameters = unserialize($a->parameters);
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

            if (strtolower($a->method_type) == 'get' && $httpbuilder) {
                $httpbuilder = "?".http_build_query($httpbuilder);
            }else{
                $httpbuilder = '';
            }

            $items[] = [
                'name' => $a->nama,
                'request' => [
                    'url' => url('api/'.$a->permalink).$httpbuilder,
                    'method' => $a->method_type ?: 'GET',
                    'header' => [],
                    'body' => [
                        'mode' => 'formdata',
                        'formdata' => $formdata,
                    ],
                    'description' => $a->keterangan,
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
        $data['page_menu'] = Route::getCurrentRoute()->getActionName();
        $data['apikeys'] = DB::table('cms_apikey')->get();

        return view('CbApiGen::api_key', $data);
    }

    public function getGenerator()
    {
        $this->cbLoader();

        $data['page_title'] = 'API Generator';
        $data['page_menu'] = Route::getCurrentRoute()->getActionName();
        $data['tables'] = getTablesList();

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
        $data['page_menu'] = Route::getCurrentRoute()->getActionName();

        $data['tables'] = getTablesList();

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

            $type_field = (array_search($ro, explode(',', cbConfig('EMAIL_FIELDS_CANDIDATE')))) ? "email" : $type_field;
            $type_field = (array_search($ro, explode(',', cbConfig('IMAGE_FIELDS_CANDIDATE')))) ? "image" : $type_field;
            $type_field = (array_search($ro, explode(',', cbConfig('PASSWORD_FIELDS_CANDIDATE')))) ? "password" : $type_field;

            $type_field = (substr($ro, -3) == '_id') ? "integer" : $type_field;
            $type_field = (substr($ro, 0, 3) == 'id_') ? "integer" : $type_field;

            $new_result[] = ['name' => $ro, 'type' => $type_field];

            if (!in_array($type, ['list', 'detail']) || substr($ro, 0, 3) !== 'id_') {
                continue;
            }
            $table2 = substr($ro, 3);
            $t2 = DB::getSchemaBuilder()->getColumnListing($table2);
            foreach ($t2 as $t) {
                if (in_array($t, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                    continue;
                }
                if (substr($t, 0, 3) == 'id_') {
                    continue;
                }

                $type_field = CRUDBooster::getFieldType($table2, $t);
                $t = str_replace("_$table2", "", $t);
                $new_result[] = ['name' => $table2.'_'.$t, 'type' => $type_field];
            }
        }

        return response()->json($new_result);
    }

    function postSaveApiCustom()
    {
        $this->cbLoader();
        $posts = Request::all();

        $a = [];

        $a['nama'] = g('nama');
        $a['tabel'] = $posts['tabel'];
        $a['aksi'] = $posts['aksi'];
        $a['permalink'] = g('permalink');
        $a['method_type'] = g('method_type');

        $params_name = g('params_name');
        $params_type = g('params_type');
        $params_config = g('params_config');
        $params_required = g('params_required');
        $params_used = g('params_used');
        $json = [];

        for ($i = 0, $_count = count($params_name); $i <= $_count; $i++) {
            if (!$params_name[$i]) {
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

        $json = array_filter($json);
        $a['parameters'] = serialize($json);

        $a['sql_where'] = g('sql_where');

        $responses_name = g('responses_name');
        $responses_type = g('responses_type');
        $responses_subquery = g('responses_subquery');
        $responses_used = g('responses_used');
        $json = [];
        for ($i = 0,$_count = count($responses_name); $i <= $_count; $i++) {
            if (!$responses_name[$i]) {
                continue;
            }
            $json[] = [
                'name' => $responses_name[$i],
                'type' => $responses_type[$i],
                'subquery' => $responses_subquery[$i],
                'used' => $responses_used[$i],
            ];
        }

        $json = array_filter($json);
        $a['responses'] = serialize($json);
        $a['keterangan'] = g('keterangan');

        if (request('id')) {
            $this->findRow(g('id'))->update($a);
        } else {

            $controllerName = ucwords(str_replace('_', ' ', $a['permalink']));
            $controllerName = str_replace(' ', '', $controllerName);
            CRUDBooster::generateAPI($controllerName, $a['tabel'], $a['permalink'], $a['method_type']);

            $this->table()->insert($a);
        }

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
}
