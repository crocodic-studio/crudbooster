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
            $fileContent = file_get_contents(controller_path($controller));
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

        $scripts = implode("\n", $this->getScriptConfig($data));
        $raw = file_get_contents(controller_path($module->controller));

        $fileController = $this->replaceConfigSection($raw, $scripts);
        file_put_contents(controller_path($module->controller), $fileController);

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

        return $scriptConfig;
    }

    /**
     * @param $raw
     * @param $scripts
     * @return string
     */
    private function replaceConfigSection($raw, $scripts)
    {
        $START = '# START CONFIGURATION DO NOT REMOVE THIS LINE';
        $END = "# END CONFIGURATION DO NOT REMOVE THIS LINE";
        $fileContent = $this->replaceBetween($raw, $scripts, $START, $END);

        return $fileContent;
    }

    /**
     * @param $raw
     * @param $scripts
     * @param $START
     * @param $END
     * @return string
     */
    private function replaceBetween($raw, $scripts, $START, $END)
    {
        list($before, $rest) = explode($START, $raw);
        list($_middle, $after) = explode($END, $rest);

        $fileContent = trim($before)."\n\n";
        $fileContent .= "            $START\n";
        $fileContent .= $scripts."\n";
        $fileContent .= "            $END\n\n";
        $fileContent .= "            ".trim($after);

        return $fileContent;
    }
}