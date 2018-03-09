<?php

namespace crocodicstudio\crudbooster\CBCoreModule\Index;

class ValueCalculator
{
    /**
     * @param $col
     * @param $row
     * @param $table
     * @return array
     */
    function calculate($col, $row, $table)
    {
        $value = @$row->{$col['field']};
        $title = @$row->{$this->cb->title_field};
        $label = $col['label'];

        list($col, $value) = $this->image($col, $table, $value, $label, $title);

        list($col, $value) = $this->download($col, $value);

        $value = $this->str_limit($col, $value);

        if ($col['nl2br']) {
            $value = nl2br($value);
        }

        if (isset($col['callback'])) {
            $value = call_user_func($col['callback'], $row);
        }

        $datavalue = @unserialize($value);
        if ($datavalue !== false && $datavalue) {
            $prevalue = [];
            foreach ($datavalue as $d) {
                if ($d['label']) {
                    $prevalue[] = $d['label'];
                }
            }
            if (count($prevalue)) {
                $value = implode(", ", $prevalue);
            }
        }

        return $value;
    }

    /**
     * @param $col
     * @param $table
     * @param $value
     * @param $label
     * @param $title
     * @return array
     */
    private function image($col, $table, $value, $label, $title)
    {
        if (!isset($col['image'])) {
            return [$col, $value];
        }
        if ($value == '') {
            $value = "<a  data-lightbox='roadtrip' rel='group_{{$table}}' title='$label: $title' href='".asset('vendor/crudbooster/avatar.jpg')."'><img width='40px' height='40px' src='".asset('vendor/crudbooster/avatar.jpg')."'/></a>";
        } else {
            $pic = (strpos($value, 'http://') !== false) ? $value : asset($value);
            $value = "<a data-lightbox='roadtrip'  rel='group_{{$table}}' title='$label: $title' href='".$pic."'><img width='40px' height='40px' src='".$pic."'/></a>";
        }

        return [$col, $value];
    }

    /**
     * @param $col
     * @param $value
     * @return array
     */
    private function download($col, $value)
    {
        if (!isset($col['download'])) {
            return [$col, $value];
        }
        $url = (strpos($value, 'http://')) ? $value : asset($value).'?download=1';
        if ($value) {
            $value = "<a class='btn btn-xs btn-primary' href='$url' target='_blank' title='Download File'><i class='fa fa-download'></i> Download</a>";
        } else {
            $value = " - ";
        }

        return [$col, $value];
    }

    /**
     * @param $col
     * @param $value
     * @return string
     */
    private function str_limit($col, $value)
    {
        if ($col['str_limit']) {
            $value = trim(strip_tags($value));
            $value = str_limit($value, $col['str_limit']);
        }

        return $value;
    }
}