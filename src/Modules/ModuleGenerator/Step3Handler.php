<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator;

use CRUDBooster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class Step3Handler
{
    public function showForm($id)
    {
        $row = DB::table('cms_moduls')->where('id', $id)->first();

        $columns = CRUDBooster::getTableColumns($row->table_name);

        $code = (readCtrlContent($row->controller));

        $forms = parseScaffoldingToArray($code, 'form');

        $types = $this->getComponentTypes();

        return view('CbModulesGen::step3', compact('columns', 'forms', 'types', 'id'));
    }

    public function handleFormSubmit()
    {
        $scripts = $this->setFormScript(Request::all());

        $controller = DB::table('cms_moduls')->where('id', request('id'))->first()->controller;
        $phpCode = readCtrlContent($controller);
        list($top, $currentScaffold, $bottom) = \CB::extractBetween($phpCode, "FORM");

        //IF FOUND OLD, THEN CLEAR IT
        $bottom = $this->clearOldBackup($bottom);

        //ARRANGE THE FULL SCRIPT
        $fileContent = $top."\n\n";
        $fileContent .= "            # START FORM DO NOT REMOVE THIS LINE\n";
        $fileContent .= $scripts;
        $fileContent .= "\n            # END FORM DO NOT REMOVE THIS LINE\n\n";

        //CREATE A BACKUP SCAFFOLDING TO OLD TAG
        if ($currentScaffold) {
            $fileContent = $this->backupOldTagScaffold($fileContent, $currentScaffold);
        }

        $fileContent .= "            ".($bottom);

        //CREATE FILE CONTROLLER
        file_put_contents(controller_path($controller), $fileContent);

        return redirect(Route("AdminModulesControllerGetStep4", ["id" => request('id')]));
    }
    /**
     * @param $fileContent
     * @param $middle
     * @return string
     */
    private function backupOldTagScaffold($fileContent, $middle)
    {
        $middle = preg_split("/\\r\\n|\\r|\\n/", $middle);
        foreach ($middle as &$c) {
            $c = "            //".trim($c);
        }
        $middle = implode("\n", $middle);

        $fileContent .= "            # OLD START FORM\n";
        $fileContent .= $middle."\n";
        $fileContent .= "            # OLD END FORM\n\n";

        return $fileContent;
    }

    /**
     * @return array
     */
    private function getComponentTypes()
    {
        $types = [];
        foreach (glob(CRUDBooster::componentsTypePath().'*', GLOB_ONLYDIR) as $dir) {
            $types[] = basename($dir);
        }

        return $types;
    }
    /**
     * @param $bottom_script
     * @return mixed
     */
    private function clearOldBackup($bottom_script)
    {
        if (strpos($bottom_script, '# OLD START FORM') !== false) {
            $line_start_old = strpos($bottom_script, '# OLD START FORM');
            $line_end_old = strpos($bottom_script, '# OLD END FORM') + strlen('# OLD END FORM');

            $get_string = substr($bottom_script, $line_start_old, $line_end_old);
            $bottom_script = str_replace($get_string, '', $bottom_script);
        }

        return $bottom_script;
    }

    /**
     * @param $post
     * @return array
     */
    private function setFormScript($post)
    {
        $labels = $post['label'];
        $name = $post['name'];
        $width = $post['width'];
        $type = $post['type'];
        $help = $post['help'];
        $placeholder = $post['placeholder'];
        $style = $post['style'];
        $validation = $post['validation'];

        $scriptForm = [];
        $scriptForm[] = "            ".'$this->form = [];';
        foreach ($labels as $i => $label) {
            if ($label == '') {
                continue;
            }
            $currentName = $name[$i];
            $form = [];
            $form['label'] = $label;
            $form['name'] = $name[$i];
            $form['type'] = $type[$i];
            $form['validation'] = $validation[$i];
            $form['width'] = $width[$i];
            $form['placeholder'] = $placeholder[$i];
            $form['help'] = $help[$i];
            $form['style'] = $style[$i];

            $info = file_get_contents(CRUDBooster::componentsTypePath().$type[$i].'/info.json');
            $info = json_decode($info, true);
            if (count($info['options'])) {
                $options = [];
                foreach ($info['options'] as $i => $opt) {
                    $optionName = $opt['name'];
                    $optionValue = $post[$optionName][$currentName];
                    if ($opt['type'] == 'array') {
                        $optionValue = ($optionValue) ? explode(";", $optionValue) : [];
                    } elseif ($opt['type'] == 'boolean') {
                        $optionValue = ($optionValue == 1) ? true : false;
                    }
                    $options[$optionName] = $optionValue;
                }
                $form['options'] = $options;
            }

            $scriptForm[] = "            ".'$this->form[] = '.min_var_export($form, "            ").";";
        }

        return implode("\n", $scriptForm);
    }
}