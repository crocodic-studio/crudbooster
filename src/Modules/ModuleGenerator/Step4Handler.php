<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator;

use crocodicstudio\crudbooster\helpers\Parsers\ControllerConfigParser;
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
            $fileContent = (readCtrlContent($controller));
            $data['config'] = ControllerConfigParser::parse($fileContent);
        }

        return view('CbModulesGen::step4', $data);
    }

    public function handleFormSubmit()
    {
        $id = Request::input('id');
        $module = DB::table('cms_moduls')->where('id', $id)->first();

        $data = Request::all();

        $data['table'] = $module->table_name;

        $newCode = $this->getScriptConfig($data);
        $rawCode = readCtrlContent($module->controller);
        $fileController = \CB::replaceBetweenMark($rawCode, 'CONFIGURATION', $newCode);
        putCtrlContent($module->controller, $fileController);

        return redirect()->route('AdminModulesControllerGetIndex')->with([
            'message' => cbTrans('alert_update_data_success'),
            'message_type' => 'success',
        ]);
    }

    /**
     * @param $data
     * @return array
     */
    private function getScriptConfig($data)
    {
        $scriptConfig = [];
        $i = 0;
        $data = array_diff_key($data, array_flip(['_token', 'id', 'submit'])); // remove keys
        foreach ($data as $key => $val) {
            if ($val == 'true' || $val == 'false') {
                $value = $val;
            } else {
                $value = "'$val'";
            }

            $scriptConfig[$i] = '            $this->'.$key.' = '.$value.';';
            $i++;
        }

        return implode("\n", $scriptConfig);
    }

    /**
     * @param $phpCode
     * @param $mark
     * @param $newCode
     * @return string
     */
    private function replaceConfigSection($phpCode, $mark, $newCode)
    {
        list($before, $_middle, $after) = \CB::extractBetween($phpCode, $mark);

        $_code = $before."\n\n";
        $_code .= "            # START $mark DO NOT REMOVE THIS LINE\n";
        $_code .= $newCode."\n";
        $_code .= "            # END $mark DO NOT REMOVE THIS LINE\n\n";
        $_code .= '            '.$after;

        return $_code;
    }
}