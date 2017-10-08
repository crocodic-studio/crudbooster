<?php
/* 
| ---------------------------------------------------------------------------------------------------------------
| Main Helper of CRUDBooster
| Do not edit or modify this helper unless your modification will be replace if any update from CRUDBooster.
| 
| Homepage : http://crudbooster.com
| ---------------------------------------------------------------------------------------------------------------
|
*/

if (! function_exists('parseControllerConfigToArray')) {
    function parseControllerConfigToArray($code)
    {
        $configCode = extract_unit($code, "# START CONFIGURATION DO NOT REMOVE THIS LINE", "# END CONFIGURATION DO NOT REMOVE THIS LINE");
        $configCode = preg_replace('/[\t\n\r\0\x0B]/', '', $configCode);
        $configCode = preg_replace('/([\s])\1+/', ' ', $configCode);
        $configCode = explode(";", $configCode);
        $configCode = array_map('trim', $configCode);
        $result = [];
        foreach ($configCode as &$code) {
            $key = substr($code, 0, strpos($code, ' = '));
            $key = str_replace('$this->', '', $key);
            $val = substr($code, strpos($code, ' = ') + 3);
            $val = trim(str_replace("'", '"', $val), '"');
            if (strtolower($val) == "true") {
                $val = true;
            } elseif (strtolower($val) == "false") {
                $val = false;
            }
            if ($key == "") {
                continue;
            }

            $result[$key] = $val;
        }

        return $result;
    }
}

if (! function_exists('parseScaffoldingToArray')) {
    function parseScaffoldingToArray($code, $type = 'form')
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
        foreach ($colsItem as &$item) {
            $item = trim($item);
            $item = trim($item, '[');
            $item = trim($item, '];');
            $item = trim($item);
            $item = trim(preg_replace("/[\n\r\t]/", "", $item));
            $strSplit = str_split($item);
            $isInner = false;
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
            if ($type == 'form') {
                if ($form['options']) {
                    @eval("\$options = $form[options];");
                    @$form['options'] = $options;
                } else {
                    $form['options'] = [];
                }
            }
        }

        return $colsItem;
    }
}

if (! function_exists('readMethodContent')) {
    function readMethodContent($code, $findMethod)
    {
        $functionToFind = $findMethod;

        $codeArray = explode("\n", $code);
        $tagBuka = 0;
        $tagTutup = 0;
        $tagPembukas = [];
        $tagPentutups = [];
        $methodIndex = [];
        $indentCount = 0;
        $methodAccessCandidate = ['public', 'private'];

        $e = 0;
        foreach ($codeArray as &$a) {

            // if($a=='') continue;

            if (strpos($a, 'function '.$functionToFind.'(') !== false) {
                $tagBuka = $e;
                $indentCount = substr_count($a, "\t");
            }

            if (stripos($a, '}') !== false) {
                $tagPentutups[$e] = $e;
            }

            if (stripos($a, '{') !== false) {
                $tagPembukas[$e] = $e;
            }

            foreach ($methodAccessCandidate as $m) {
                if (strpos($a, $m) !== false) {
                    $methodIndex[] = $e;
                    break;
                }
            }

            if (strpos($a, ' function ') !== false) {
                $methodIndex[] = $e;
            }

            $e++;
        }

        $methodIndex = array_unique($methodIndex);
        $methodIndex = array_values($methodIndex); //reset keys

        $keyTagBukaInMethodIndex = array_search($tagBuka, $methodIndex);
        $totalMethodIndex = count($methodIndex) - 1;
        $methodNextIndex = ($totalMethodIndex == $keyTagBukaInMethodIndex) ? $keyTagBukaInMethodIndex : $keyTagBukaInMethodIndex + 1;

        $findIndexPenutup = 0;
        $tagPentutups = array_values($tagPentutups);

        if ($tagBuka == end($methodIndex)) {
            $finalTagPenutup = $tagPentutups[count($tagPentutups) - 2];
        } else {
            foreach ($tagPentutups as $i => $tp) {
                if ($tp > $methodIndex[$methodNextIndex]) {
                    $finalTagPenutup = $tagPentutups[$i - 1];
                    break;
                }
            }
        }

        // echo "Tag Buka: ".$tagBuka."<br/>";
        // echo "Tag Pembukas: ".implode(",",$tagPembukas)."<br/>";
        // echo "Tag Pentutups: ".implode(",",$tagPentutups)."<br/>";
        // echo "Tag Penutups Last:".end($tagPentutups)."<br/>";
        // echo "Tag Tutup: ".$tagTutup."<br/>";
        // echo "Method Index:".implode(",",$methodIndex)."<br/>";
        // echo "Method Index Last:".end($methodIndex)."<br/>";
        // echo "Method Next Index: ".$methodNextIndex."<br/>";
        // echo "Method Next Index Val: ".$methodIndex[$methodNextIndex]."<br/>";

        // echo "Final Tag Pembuka: ".$tagBuka."<br/>";
        // echo "Final Tag Penutup: ".$finalTagPenutup."<br/>";

        $content = [];
        foreach ($codeArray as $i => $c) {
            if ($i > $tagBuka && $i < $finalTagPenutup) {
                $content[] = $c;
            }
        }

        return implode("\n", $content);
        // // dd($codeArray);
        // echo '<pre>';
        // print_r($codeArray);
    }
}

if (! function_exists('writeMethodContent')) {
    function writeMethodContent($code, $findMethod, $stringContent)
    {
        $functionToFind = $findMethod;
        $stringToInsert = $stringContent;

        $codeArray = explode("\n", $code);
        $tagBuka = 0;
        $tagTutup = 0;
        $tagPembukas = [];
        $tagPentutups = [];
        $methodIndex = [];
        $indentCount = 0;
        $methodAccessCandidate = ['public', 'private'];

        $e = 0;
        foreach ($codeArray as &$a) {

            // if($a=='') continue;

            if (strpos($a, 'function '.$functionToFind.'(') !== false) {
                $tagBuka = $e;
                $indentCount = substr_count($a, "\t");
            }

            if (stripos($a, '}') !== false) {
                $tagPentutups[$e] = $e;
            }

            if (stripos($a, '{') !== false) {
                $tagPembukas[$e] = $e;
            }

            foreach ($methodAccessCandidate as $m) {
                if (strpos($a, $m) !== false) {
                    $methodIndex[] = $e;
                    break;
                }
            }

            if (strpos($a, ' function ') !== false) {
                $methodIndex[] = $e;
            }

            $e++;
        }

        $methodIndex = array_unique($methodIndex);
        $methodIndex = array_values($methodIndex); //reset keys

        $keyTagBukaInMethodIndex = array_search($tagBuka, $methodIndex);
        $totalMethodIndex = count($methodIndex) - 1;
        $methodNextIndex = ($totalMethodIndex == $keyTagBukaInMethodIndex) ? $keyTagBukaInMethodIndex : $keyTagBukaInMethodIndex + 1;

        $findIndexPenutup = 0;
        $tagPentutups = array_values($tagPentutups);
        if ($tagBuka == end($methodIndex)) {
            $finalTagPenutup = $tagPentutups[count($tagPentutups) - 2];
        } else {
            foreach ($tagPentutups as $i => $tp) {
                if ($tp > $methodIndex[$methodNextIndex]) {
                    $finalTagPenutup = $tagPentutups[$i - 1];
                    break;
                }
            }
        }

        // echo "Tag Buka: ".$tagBuka."<br/>";
        // echo "Tag Pembukas: ".implode(",",$tagPembukas)."<br/>";
        // echo "Tag Pentutups: ".implode(",",$tagPentutups)."<br/>";
        // echo "Tag Tutup: ".$tagTutup."<br/>";
        // echo "Method Index:".implode(",",$methodIndex)."<br/>";
        // echo "Method Next Index: ".$methodNextIndex."<br/>";
        // echo "Method Next Index Val: ".$methodIndex[$methodNextIndex]."<br/>";

        // echo "Final Tag Pembuka: ".$tagBuka."<br/>";
        // echo "Final Tag Penutup: ".$finalTagPenutup."<br/>";

        //Removing Content Of Method
        foreach ($codeArray as $i => $c) {
            if ($i > $tagBuka && $i < $finalTagPenutup) {
                unset($codeArray[$i]);
            }
        }

        //Insert Content To Method
        $stringToInsertArray = explode("\n", trim($stringToInsert));
        foreach ($stringToInsertArray as $i => &$s) {
            $s = str_repeat("\t", $indentCount + 2).trim($s);
        }

        $stringToInsert = implode("\n", $stringToInsertArray);
        foreach ($codeArray as $i => $c) {
            if ($i == $tagBuka) {
                array_splice($codeArray, $i + 1, 0, [$stringToInsert]);
            }
        }

        return implode("\n", $codeArray);
    }
}

if (! function_exists('rrmdir')) {
    function rrmdir($dir)
    {
        if (!is_dir($dir)) {
            return;
        }
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object == "." || $object == "..") {
                continue;
            }
            if (is_dir($dir."/".$object)) {
                rrmdir($dir."/".$object);
            } else {
                unlink($dir."/".$object);
            }
        }
        rmdir($dir);
    }
}

if (! function_exists('extract_unit')) {
    /*
    Credits: Bit Repository
    URL: http://www.bitrepository.com/extract-content-between-two-delimiters-with-php.html
    */
    function extract_unit($string, $start, $end)
    {
        $pos = stripos($string, $start);
        $str = substr($string, $pos);
        $str_two = substr($str, strlen($start));
        $second_pos = stripos($str_two, $end);
        $str_three = substr($str_two, 0, $second_pos);
        $unit = trim($str_three); // remove whitespaces

        return $unit;
    }
}

if (! function_exists('now')) {
    function now()
    {
        return date('Y-m-d H:i:s');
    }
}

/* 
| --------------------------------------------------------------------------------------------------------------
| Get data from input post/get more simply
| --------------------------------------------------------------------------------------------------------------
| $name = name of input
|
*/
if (! function_exists('g')) {
    function g($name)
    {
        return Request::get($name);
    }
}

if (! function_exists('min_var_export')) {
    function min_var_export($input, $indent = "")
    {
        if (!is_array($input)) {
            return var_export($input, true);
        }
        $buffer = [];
        foreach ($input as $key => $value) {
            $buffer[] = $indent.var_export($key, true)."=>".min_var_export($value, ($indent."\t"));
        }
        if (count($buffer) == 0) {
            return "[]";
        }
        return "[\n".implode(",\n", $buffer)."\n$indent]";
    }
}

if (! function_exists('rrmdir')) {
    /*
    * http://stackoverflow.com/questions/3338123/how-do-i-recursively-delete-a-directory-and-its-entire-contents-files-sub-dir
    */
    function rrmdir($dir)
    {
        if (!is_dir($dir)) {
            return;
        }
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object == "." || $object == "..") {
                continue;
            }
            if (is_dir($dir."/".$object)) {
                rrmdir($dir."/".$object);
            } else {
                unlink($dir."/".$object);
            }
        }
        rmdir($dir);
    }
}

if (! function_exists('cbTrans')) {
    function cbTrans($key, $params = [])
    {
        return trans('crudbooster.'.$key, $params);
    }
}

if (! function_exists('cbAsset')) {
    function cbAsset($key)
    {
        return asset('vendor/crudbooster/assets/'.$key);
    }
}

if (! function_exists('cbScript')) {
    function cbScript($key)
    {
        return '<script src="'.cbAsset($key).'" type="text/javascript"></script>';
    }
}

if (! function_exists('cbStyleSheet')) {
    function cbStyleSheet($key)
    {
        return '<link rel="stylesheet" type="text/css" href="'.cbAsset($key).'"/>';
    }
}

if (! function_exists('cbConfig')) {
    function cbConfig($key)
    {
        return config('crudbooster.'.$key);
    }
}