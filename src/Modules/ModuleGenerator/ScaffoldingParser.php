<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator;

class ScaffoldingParser
{
    static function parse($code, $type = 'form')
    {
        $colsItem = self::extractLines($code, $type);

        foreach ($colsItem as &$item) {
            $item = self::removeExtraCharacters($item);
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
            $col = self::prepareFields(explode('|SPLIT|', $col));
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
            $d = 'FORM';
        } elseif ($type == 'col') {
            $d = 'COLUMNS';
        }

        $cols = self::extract_unit($code, "# START $d DO NOT REMOVE THIS LINE", "# END $d DO NOT REMOVE THIS LINE");
        $cols = str_replace('"', "'", $cols);
        $cols = trim(str_replace('$this->'.$type.' = [];', '', $cols));
        $colsItem = explode('$this->'.$type.'[] = ', $cols);

        return array_filter($colsItem);
    }

    static function extract_unit($string, $start, $end)
    {
        $pos = stripos($string, $start);
        $str = substr($string, $pos);
        $str_two = substr($str, strlen($start));
        $second_pos = stripos($str_two, $end);
        $str_three = substr($str_two, 0, $second_pos);
        $unit = trim($str_three); // remove whitespaces

        return $unit;
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
                $colInnerItem['options'] = trim(str_replace("'options'=>", "", $s), '\'\"\]\[');
            } elseif (strpos($s, 'callback') !== false) {
                $colInnerItem['callback'] = self::parseCallback($s);
            } else {
                $s = str_replace("'", '',$s);
                $sSplit = explode('=>', $s);
                $colInnerItem[$sSplit[0]] = $sSplit[1];
            }
        }

        return $colInnerItem;
    }

    /**
     * @param $item
     * @return mixed|string
     */
    private static function removeExtraCharacters($item)
    {
        $item = str_replace(' ', '', $item);
        // "['label'=>'KanapeType','name'=>'kanape_type',];\r\n"

        $item = str_replace('\',]', ']', $item); // replaces:  ',]  with   ]
        // "['label'=>'KanapeType','name'=>'kanape_type];\r\n"

        $item = trim($item);
        // "['label'=>'KanapeType','name'=>'kanape_type];"

        $item = trim($item, '[');
        // "'label'=>'KanapeType','name'=>'kanape_type];"

        $item = trim($item, '];');
        // "'label'=>'KanapeType','name'=>'kanape_type"

        $item = trim($item);
        $item = trim(preg_replace("/[\n\r\t]/", "", $item));

        return $item;
    }
}