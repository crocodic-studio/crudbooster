<?php

namespace crocodicstudio\crudbooster\Modules\ApiGeneratorModule;

use crocodicstudio\crudbooster\controllers\CBController;
use crocodicstudio\crudbooster\helpers\CbValidator;
use Illuminate\Support\Facades\DB;

class AdminApiKeyController extends CBController
{
    public function cbInit()
    {
        $this->table = 'cms_apicustom';
        $this->primaryKey = "id";
        $this->titleField = "nama";
        $this->buttonShow = false;
        $this->button_new = false;
        $this->deleteBtn = false;
        $this->buttonAdd = false;
        $this->button_import = false;
        $this->buttonExport = false;
    }

    public function getSecretKey()
    {
        $this->cbLoader();
        $data['page_title'] = 'API Generator';
        $data['apikeys'] = DB::table('cms_apikey')->get();

        return view('CbApiGen::api_key', $data);
    }

    function getGenerateSecretKey()
    {
        $this->cbLoader();
        //Generate a random string.
        $token = openssl_random_pseudo_bytes(16);

        //Convert the binary data into hexadecimal representation.
        $token = bin2hex($token);

        $id = DB::table('cms_apikey')->insertGetId([
                'secretkey' => $token,
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
        CbValidator::valid(['id' => 'required', 'status' => 'required'], 'view');

        $id = request('id');
        $status = (request('status') == 1) ? "active" : "non active";

        DB::table('cms_apikey')->where('id', $id)->update(['status' => $status]);

        backWithMsg('You have been update api key status !');
    }

    public function getDeleteApiKey()
    {
        $id = request('id');
        if (DB::table('cms_apikey')->where('id', $id)->delete()) {
            return response()->json(['status' => 1]);
        }

        return response()->json(['status' => 0]);
    }

}
