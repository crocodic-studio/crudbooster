<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator;

class Step4Handler
{
    public function showForm($id)
    {
        $controller = ModulesRepo::getControllerName($id);;

        $data = [];
        $data['id'] = $id;
        if (file_exists(controller_path($controller))) {
            $fileContent = FileManipulator::readCtrlContent($controller);
            $data['config'] = ControllerConfigParser::parse($fileContent);
        }

        return view('CbModulesGen::step4', $data);
    }

    public function handleFormSubmit()
    {
        $id = request('id');
        $module = ModulesRepo::find($id);

        $data = request()->all();

        $data['table'] = $module->table_name;

        $newCode = $this->getScriptConfig($data);
        $this->replaceInFile($module->controller, 'CONFIGURATION', $newCode);

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

            $scriptConfig[$i] = str_repeat(' ', 12).'$this->'.$key.' = '.$value.';';
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
/*    private function replaceConfigSection($phpCode, $mark, $newCode)
    {
        list($before, $_middle, $after) = FileManipulator::extractBetween($phpCode, $mark);

        $_code = $before."\n\n";
        $_code .= "            # START $mark DO NOT REMOVE THIS LINE\n";
        $_code .= $newCode."\n";
        $_code .= "            # END $mark DO NOT REMOVE THIS LINE\n\n";
        $_code .= '            '.$after;

        return $_code;
    }*/

    /**
     * @param $controller
     * @param $mark
     * @param $newCode
     */
    private function replaceInFile($controller, $mark, $newCode)
    {
        $rawCode = FileManipulator::readCtrlContent($controller);
        $fileController = FileManipulator::replaceBetweenMark($rawCode, $mark, $newCode);
        FileManipulator::putCtrlContent($controller, $fileController);
    }
}