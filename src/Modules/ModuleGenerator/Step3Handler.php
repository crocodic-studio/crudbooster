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

        $code = file_get_contents(controller_path($row->controller));

        $forms = parseScaffoldingToArray($code, 'form');

        $types = $this->getComponentTypes();

        return view('CbModulesGen::step3', compact('columns', 'forms', 'types', 'id'));
    }

    public function handleFormSubmit()
    {
        $post = Request::all();

        $script_form = $this->setFormScript($post);
        $row = DB::table('cms_moduls')->where('id', request('id'))->first();
        $scripts = implode("\n", $script_form);
        $raw = file_get_contents(controller_path($row->controller));
        $raw = explode("# START FORM DO NOT REMOVE THIS LINE", $raw);
        $rraw = explode("# END FORM DO NOT REMOVE THIS LINE", $raw[1]);

        $top_script = trim($raw[0]);
        $current_scaffolding_form = trim($rraw[0]);
        $bottom_script = trim($rraw[1]);

        //IF FOUND OLD, THEN CLEAR IT
        $bottom_script = $this->clearOldBackup($bottom_script);

        //ARRANGE THE FULL SCRIPT
        $file_controller = $top_script."\n\n";
        $file_controller .= "            # START FORM DO NOT REMOVE THIS LINE\n";
        $file_controller .= "            ".'$this->form = [];'."\n";
        $file_controller .= $scripts."\n";
        $file_controller .= "            # END FORM DO NOT REMOVE THIS LINE\n\n";

        //CREATE A BACKUP SCAFFOLDING TO OLD TAG
        $file_controller = $this->backupOldTagScaffold($file_controller, $current_scaffolding_form);

        $file_controller .= "            ".trim($bottom_script);

        //CREATE FILE CONTROLLER
        file_put_contents(controller_path($row->controller), $file_controller);

        return redirect(Route("AdminModulesControllerGetStep4", ["id" => request('id')]));
    }
    /**
     * @param $file_controller
     * @param $current_scaffolding_form
     * @return array
     */
    private function backupOldTagScaffold($file_controller, $current_scaffolding_form)
    {
        if ($current_scaffolding_form) {
            $current_scaffolding_form = preg_split("/\\r\\n|\\r|\\n/", $current_scaffolding_form);
            foreach ($current_scaffolding_form as &$c) {
                $c = "            //".trim($c);
            }
            $current_scaffolding_form = implode("\n", $current_scaffolding_form);

            $file_controller .= "            # OLD START FORM\n";
            $file_controller .= $current_scaffolding_form."\n";
            $file_controller .= "            # OLD END FORM\n\n";
        }

        return $file_controller;
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

        $script_form = [];
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

            $script_form[] = "            ".'$this->form[] = '.min_var_export($form, "            ").";";
        }

        return $script_form;
    }


}