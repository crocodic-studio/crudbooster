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
            $colProperties = self::addProperties($colProperties, $isImage[$i], $isDownload[$i], $callback[$i], $width[$i]);

            $columnScript[] = $indent.'$this->col[] = ['.implode(", ", $colProperties).'];';
        }
        return implode("\n", $columnScript);
    }

    /**
     * @param $colProperties
     * @param $isImage
     * @param $isDownload
     * @param $callback
     * @param $width
     * @return array
     */
    private static function addProperties($colProperties, $isImage, $isDownload, $callback, $width)
    {
        if ($isImage) {
            $colProperties[] = '"image" => true ';
        }
        if ($isDownload) {
            $colProperties[] = '"download" => true';
        }
        if ($callback) {
            $colProperties[] = '"callback" => function($row) {'.$callback.'}';
        }
        if ($width) {
            $colProperties[] = "'width' => '$width'";
        }

        return $colProperties;
    }
}