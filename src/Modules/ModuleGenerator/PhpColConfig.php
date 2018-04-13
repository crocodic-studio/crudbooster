<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator;

class PhpColConfig
{
    /**
     * @return array
     */
    public static function makeColumnPhpCode()
    {
        $labels = request('column');
        $name = request('name');
        $isImage = request('is_image');
        $isDownload = request('is_download');
        $callback = request('callback');
        $width = request('width');

        $indent = str_repeat(' ', 8);

        $columnScript = [];
        $columnScript[] = $indent.'$this->col[] = [];';
        foreach ($labels as $i => $label) {

            if (! $name[$i]) {
                continue;
            }

            $colProperties = ["'label' => '$label'", "'name' => '{$name[$i]}'"];
            if ($isImage[$i]) {
                $colProperties[] = '"image" => true ';
            }
            if ($isDownload[$i]) {
                $colProperties[] = '"download" => true';
            }
            if ($callback[$i]) {
                $colProperties[] = '"callback" => function($row) {'.$callback[$i].'}';
            }
            if ($width[$i]) {
                $colProperties[] = "'width' => '$width[$i]'";
            }

            $columnScript[] = $indent.'$this->col[] = ['.implode(", ", $colProperties).'];';
        }
        return implode("\n", $columnScript);
    }

}