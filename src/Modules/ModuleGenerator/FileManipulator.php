<?php

namespace Crocodicstudio\Crudbooster\Modules\ModuleGenerator;

class FileManipulator
{
    public static function putCtrlContent($ctrl, $fileContent)
    {
        return file_put_contents(controller_path($ctrl), $fileContent);
    }

    public static function readCtrlContent($ctrl)
    {
        return file_get_contents(controller_path($ctrl));
    }

    public static function extractBetween($raw, $mark)
    {
        list($before, $_rest) = explode(cbStartMarker($mark), $raw);
        list($_middle, $after) = explode(cbEndMarker($mark), $_rest);

        return [trim($before), trim($_middle), trim($after)];
    }

    /**
     * @param $phpCode
     * @param $mark
     * @param $newCode
     * @return string
     */
    public static function replaceBetweenMark($phpCode, $mark, $newCode)
    {
        list($top, $_middle, $bottom) = self::extractBetween($phpCode, $mark);
        unset($_middle);

        $_code = $top."\n\n";
        $indent = str_repeat(' ', 8);

        $_code .= $indent.cbStartMarker($mark)."\n";
        $_code .= $indent.$newCode."\n";
        $_code .= $indent.cbEndMarker($mark)."\n\n";
        $_code .= $indent.$bottom;

        return $_code;
    }

    public static function stringify($input, $indent = "")
    {
        if (! is_array($input)) {
            return var_export($input, true);
        }
        $buffer = [];
        foreach ($input as $key => $value) {
            $buffer[] = $indent.var_export($key, true)."=>".self::stringify($value, ($indent."    "));
        }
        if (empty($buffer)) {
            return "[]";
        }

        return "[\n".implode(",\n", $buffer)."\n$indent]";
    }
}