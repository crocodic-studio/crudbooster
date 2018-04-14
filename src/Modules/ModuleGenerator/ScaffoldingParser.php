<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator;

class ScaffoldingParser
{
    public static function parse($code, $type = 'form')
    {
        $colsItem = self::extractLines($code, $type);

        foreach ($colsItem as &$item) {
            $item = self::removeExtraCharacters($item);
            $strSplit = str_split($item);
            $strSplit = self::putDelimiter($strSplit);
            $item = implode("", $strSplit);
        }

        foreach ($colsItem as &$col) {
            $col = self::prepareFields(explode('|SPLIT|', $col));
        }

        $colsItem = self::formOptions($type, $colsItem);

        return $colsItem;
    }

    /**
     * @param $code
     * @param $type
     * @return array
     */
    private static function extractLines($code, $type)
    {
        $mark = 'COLUMNS';
        if ($type == 'form') {
            $mark = 'FORM';
        }

        $cols = self::extract_unit($code, cbStartMarker($mark), cbEndMarker($mark));
        $cols = str_replace('"', "'", $cols);
        $cols = trim(str_replace('$this->'.$type.' = [];', '', $cols));
        $colsItem = explode('$this->'.$type.'[] = ', $cols);

        return array_filter($colsItem);
    }

    public static function extract_unit($string, $start, $end)
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
        if ($type !== 'form') {
            return $colsItem;
        }
        foreach ($colsItem as $i => $form) {
            if ( empty($form['options']) == false && $form['options'] !== '[]') {
                @eval("\$options = $form[options];");
                @$colsItem[$i]['options'] = $options;
            } else {
                $colsItem[$i]['options'] = [];
            }
        }

        return $colsItem;

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
                continue;
            }
            if (strpos($s, 'callback') !== false) {
                $colInnerItem['callback'] = self::parseCallback($s);
                continue;
            }

            $s = str_replace("'", '',$s);
            list($key, $val) = explode('=>', $s);
            $colInnerItem[$key] = $val;

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

    /**
     * @param $strSplit
     * @return mixed
     */
    private static function putDelimiter($strSplit)
    {
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

        return $strSplit;
    }
}