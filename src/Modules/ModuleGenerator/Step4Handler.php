<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class Step4Handler
{
    public function showForm($id)
    {
        $controller = DB::table('cms_moduls')->where('id', $id)->first()->controller;

        $data = [];
        $data['id'] = $id;
        if (file_exists(controller_path($controller))) {
            $response = file_get_contents(controller_path($controller));
            $data['config'] = parseControllerConfigToArray($response);
        }

        return view('CbModulesGen::step4', $data);
    }

    public function handleFormSubmit()
    {
        $id = Request::input('id');
        $row = DB::table('cms_moduls')->where('id', $id)->first();

        $post = Request::all();

        $post['table'] = $row->table_name;

        $script_config = [];
        $exception = ['_token', 'id', 'submit'];
        $i = 0;
        foreach ($post as $key => $val) {
            if (in_array($key, $exception)) {
                continue;
            }

            if ($val != 'true' && $val != 'false') {
                $value = '"'.$val.'"';
            } else {
                $value = $val;
            }

            $script_config[$i] = "            ".'$this->'.$key.' = '.$value.';';
            $i++;
        }

        $scripts = implode("\n", $script_config);
        $raw = file_get_contents(controller_path($row->controller));
        $raw = explode("# START CONFIGURATION DO NOT REMOVE THIS LINE", $raw);
        $rraw = explode("# END CONFIGURATION DO NOT REMOVE THIS LINE", $raw[1]);

        $file_controller = trim($raw[0])."\n\n";
        $file_controller .= "            # START CONFIGURATION DO NOT REMOVE THIS LINE\n";
        $file_controller .= $scripts."\n";
        $file_controller .= "            # END CONFIGURATION DO NOT REMOVE THIS LINE\n\n";
        $file_controller .= "            ".trim($rraw[1]);

        file_put_contents(controller_path($row->controller), $file_controller);

        return redirect()->route('AdminModulesControllerGetIndex')->with([
            'message' => trans('crudbooster.alert_update_data_success'),
            'message_type' => 'success',
        ]);
    }
}