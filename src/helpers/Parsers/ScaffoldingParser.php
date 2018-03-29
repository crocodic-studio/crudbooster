<?php

namespace crocodicstudio\crudbooster\helpers\Parsers;

class ScaffoldingParser
{
    static function parse($code, $type = 'form')
    {
        $colsItem = self::extractLines($code, $type);

        foreach ($colsItem as &$item) {
            $item = str_replace(' ','', $item);
            $item = str_replace('\',]',']', $item);
            $item = trim($item);
            $item = trim($item, '[');
            $item = trim($item, '];');
            $item = trim($item);
            $item = trim(preg_replace("/[\n\r\t]/", "", $item));
            $strSplit = str_split($item);
            $innerCount = 0;
            foreach ($strSplit as $index => $s) {
                if ($s == '[') {
                    $innerCount++;
                }
                if ($s == ']') {
                    $innerCount--;
                }
                if ($innerCount == 0 && $s == ',' && $strSplit[$index + 1] == "'") {
                    $strSplit[$index] = "|SPLIT|";
                }
            }
            $item = implode("", $strSplit);
        }

        foreach ($colsItem as &$col) {
            $colInnerItem = self::prepareFields(explode('|SPLIT|', $col));
            $col = $colInnerItem;
        }

        self::formOptions($type, $colsItem);

        return $colsItem;
    }

    /**
     * @param $code
     * @param $type
     * @return array
     */
    private static function extractLines($code, $type)
    {
        if ($type == 'form') {
            $cols = extract_unit($code, "# START FORM DO NOT REMOVE THIS LINE", "# END FORM DO NOT REMOVE THIS LINE");
            $cols = str_replace('"', "'", $cols);
            $cols = trim(str_replace('$this->form = [];', '', $cols));
            $colsItem = explode('$this->form[] = ', $cols);
        } elseif ($type == 'col') {
            $cols = extract_unit($code, "# START COLUMNS DO NOT REMOVE THIS LINE", "# END COLUMNS DO NOT REMOVE THIS LINE");
            $cols = str_replace('"', "'", $cols);
            $cols = trim(str_replace('$this->col = [];', '', $cols));
            $colsItem = explode('$this->col[] = ', $cols);
        }

        $colsItem = array_filter($colsItem);

        return $colsItem;
    }

    /**
     * @param $type
     * @param $colsItem
     * @return mixed
     */
    private static function formOptions($type, $colsItem)
    {
        foreach ($colsItem as &$form) {
            if ($type !== 'form') {
                continue;
            }
            if ($form['options']) {
                @eval("\$options = $form[options];");
                @$form['options'] = $options;
            } else {
                $form['options'] = [];
            }
        }
    }

    /**
     * @param $s
     * @return array
     */
    private static function parseCallback($s)
    {
        $s = str_replace("return", "return ", $s);
        $val = trim(str_replace(["'callback'=>function(\$row) {", "'callback'=>function(\$row){"], "", $s));
        $val = substr($val, 0, -1);

        return $val; //to remove last }
    }

    /**
     * @param $split
     * @return array
     */
    private static function prepareFields($split)
    {
        $colInnerItem = [];
        foreach ($split as $s) {
            if (strpos($s, 'options') !== false) {
                $colInnerItem['options'] = trim(str_replace("'options'=>", "", $s));
            } elseif (strpos($s, 'callback') !== false) {
                $colInnerItem['callback'] = self::parseCallback($s);
            } else {
                $s = trim($s, "'");
                $sSplit = explode('=>', $s);
                $colInnerItem[$sSplit[0]] = $sSplit[1];
            }
        }

        return $colInnerItem;
    }
}