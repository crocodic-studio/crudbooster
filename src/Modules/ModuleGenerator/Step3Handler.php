<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator;

use CRUDBooster;

class Step3Handler
{
    public function showForm($id)
    {
        $row = ModulesRepo::find($id);;

        $columns = CRUDBooster::getTableColumns($row->table_name);

        $code = FileManipulator::readCtrlContent($row->controller);

        $forms = ScaffoldingParser::parse($code, 'form');

        $types = $this->getComponentTypes();

        return view('CbModulesGen::step3', compact('columns', 'forms', 'types', 'id'));
    }

    /**
     * @return array
     */
    private function getComponentTypes()
    {
        $types = [];
        foreach (glob(CB::componentsPath().'*', GLOB_ONLYDIR) as $dir) {
            array_push($types, basename($dir));
        }
        return $types;
    }

    public function handleFormSubmit()
    {
        $scripts = $this->setFormScript(request()->all());

        $controller = ModulesRepo::getControllerName(request('id'));
        $phpCode = FileManipulator::readCtrlContent($controller);
        list($top, $currentScaffold, $bottom) = FileManipulator::extractBetween($phpCode, "FORM");

        //IF FOUND OLD, THEN CLEAR IT
        $bottom = $this->clearOldBackup($bottom);

        //ARRANGE THE FULL SCRIPT
        $fileContent = $top."\n\n";
        $fileContent .= str_repeat(' ', 12)."# START FORM DO NOT REMOVE THIS LINE\n";
        $fileContent .= $scripts;
        $fileContent .= "\n".str_repeat(' ', 12)."# END FORM DO NOT REMOVE THIS LINE\n\n";

        //CREATE A BACKUP SCAFFOLDING TO OLD TAG
        if ($currentScaffold) {
            $fileContent = $this->backupOldTagScaffold($fileContent, $currentScaffold);
        }

        $fileContent .= str_repeat(' ', 12).($bottom);

        //CREATE FILE CONTROLLER
        FileManipulator::putCtrlContent($controller, $fileContent);

        return redirect()->route('AdminModulesControllerGetStep4', ['id' => request('id')]);
    }

    /**
     * @param $post
     * @return array
     */
    private function setFormScript($post)
    {
        $name = $post['name'];
        $width = $post['width'];
        $type = $post['type'];
        $help = $post['help'];
        $placeholder = $post['placeholder'];
        $style = $post['style'];
        $validation = $post['validation'];

        $scriptForm = [];
        $scriptForm[] = str_repeat(' ', 12).'$this->form = [];';

        foreach ($post['label'] as $i => $label) {
            if ($label == '') {
                continue;
            }
            $form = [
                'label' => $label,
                'name' => $name[$i],
                'type' => $type[$i],
                'validation' => $validation[$i],
                'width' => $width[$i],
                'placeholder' => $placeholder[$i],
                'help' => $help[$i],
                'style' => $style[$i],
            ];

            $info = json_decode(file_get_contents(CRUDBooster::componentsPath($type[$i]).'/info.json'), true);
            if (!empty($info['options'])) {
                $form = $this->parseComponentOptions($post, $info, $form);
            }

            $scriptForm[] = str_repeat(' ', 12).'$this->form[] = '.FileManipulator::stringify($form, str_repeat(' ', 12)).';';
        }

        return implode("\n", $scriptForm);
    }

    /**
     * @param $bottomScript
     * @return mixed
     */
    private function clearOldBackup($bottomScript)
    {
        if (strpos($bottomScript, '# OLD START FORM') === false) {
            return $bottomScript;
        }
        $lineStart = strpos($bottomScript, '# OLD START FORM');
        $lineEnd = strpos($bottomScript, '# OLD END FORM') + strlen('# OLD END FORM');

        $getString = substr($bottomScript, $lineStart, $lineEnd);

        return str_replace($getString, '', $bottomScript);
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
            $c = str_repeat(' ', 12)."//".trim($c);
        }
        $middle = implode("\n", $middle);

        $fileContent .= str_repeat(' ', 12)."# OLD START FORM\n";
        $fileContent .= $middle."\n";
        $fileContent .= str_repeat(' ', 12)."# OLD END FORM\n\n";

        return $fileContent;
    }

    /**
     * @param $post
     * @param $info
     * @param $form
     * @return array
     */
    private function parseComponentOptions($post, $info, $form)
    {
        $options = [];
        foreach ($info['options'] as $i => $opt) {
            $optionValue = $post[$opt['name']][$form['name']];
            if ($opt['type'] == 'array') {
                $options[$opt['name']] = ($optionValue) ? explode(";", $optionValue) : [];
            } elseif ($opt['type'] == 'boolean') {
                $options[$opt['name']] = ($optionValue == 1) ? true : false;
            }
        }
        $form['options'] = $options;

        return $form;
    }
}