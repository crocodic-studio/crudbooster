<?php

namespace Crocodicstudio\Crudbooster\CBCoreModule\Index;

class ValueCalculator
{
    /**
     * @param $col
     * @param $row
     * @param $title
     * @return string
     */
    public function calculate($col, $row, $title)
    {
        $value = $row->{$col['field']};
        $label = $col['label'];

        if (isset($col['image'])) {
            $value = $this->image($value, $label, $title);
        }

        if (isset($col['download'])) {
            $value = $this->download($value);
        }

        if (isset($col['str_limit'])) {
            $value = $this->str_limit($col['str_limit'], $value);
        }

        if ($col['nl2br'] ?? false) {
            $value = nl2br($value);
        }

        if (isset($col['callback'])) {
            $value = call_user_func($col['callback'], $row);
        }

        return $this->includeLabels($value);
    }

    /**
     * @param $value
     * @return string
     */
    private function download($value)
    {
        $url = (strpos($value, 'http://')) ? $value : asset($value).'?download=1';
        if (! $value) {
            return " - ";
        }
        return "<a class='btn btn-xs btn-primary' href='$url' target='_blank' title='Download File'><i class='fa fa-download'>".cbTrans('button_download_file')."</i></a>";
    }

    /**
     * @param $value
     * @param $label
     * @param $title
     * @return string
     */
    private function image($value, $label, $title)
    {
        if ($value == '') {
            return "<a  data-lightbox='roadtrip' title='$label: $title' href='".asset('vendor/crudbooster/avatar.jpg')."'><img width='40px' height='40px' src='".asset('vendor/crudbooster/avatar.jpg')."'/></a>";
        }
        $pic = (strpos($value, 'http://') !== false) ? $value : asset($value);
        return "<a data-lightbox='roadtrip' title='$label: $title' href='$pic'><img width='40px' height='40px' src='$pic'/></a>";
    }

    /**
     * @param $col
     * @param $value
     * @return string
     */
    private function str_limit($limit, $value)
    {
        return str_limit(trim(strip_tags($value)), $limit);
    }

    /**
     * @param $value
     * @return string
     */
    private function includeLabels($value)
    {
        $datavalue = @unserialize($value);
        if (!$datavalue) {
            return $value;
        }
        $preValue = [];
        foreach ($datavalue as $d) {
            if ($d['label']) {
                $preValue[] = $d['label'];
            }
        }
        if (!empty($preValue)) {
            $value = implode(", ", $preValue);
        }

        return $value;
    }
}