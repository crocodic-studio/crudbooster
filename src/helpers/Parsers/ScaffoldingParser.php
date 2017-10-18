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
            foreach ($strSplit as $e => $s) {
                if ($s == '[') {
                    $innerCount++;
                }
                if ($s == ']') {
                    $innerCount--;
                }
                if ($innerCount == 0) {
                    if ($s == ',') {
                        if ($strSplit[$e + 1] == "'") {
                            $strSplit[$e] = "|SPLIT|";
                        }
                    }
                }
            }
            $item = implode("", $strSplit);
        }
        foreach ($colsItem as &$col) {
            $split = explode('|SPLIT|', $col);

            $colInnerItem = [];
            foreach ($split as $s) {
                if (strpos($s, 'options') !== false) {
                    $key = 'options';
                    $val = trim(str_replace("'options'=>", "", $s));
                } elseif (strpos($s, 'callback')) {
                    $key = 'callback';
                    $val = trim(str_replace(["'callback'=>function(\$row) {", "'callback'=>function(\$row){"], "", $s));
                    $val = substr($val, 0, -1); //to remove last }
                } else {
                    $sSplit = explode('=>', $s);
                    $key = trim($sSplit[0], "'");
                    $val = trim($sSplit[1], "'");
                }
                $colInnerItem[$key] = $val;
            }
            $col = $colInnerItem;

        }


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
}