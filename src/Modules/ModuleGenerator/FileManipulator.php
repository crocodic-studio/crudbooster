<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator;

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
        list($before, $_rest) = explode("# START $mark DO NOT REMOVE THIS LINE", $raw);
        list($_middle, $after) = explode("# END $mark DO NOT REMOVE THIS LINE", $_rest);

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

        $_code = $top."\n\n";
        $_code .= str_repeat(' ', 12)."# START $mark DO NOT REMOVE THIS LINE\n";
        $_code .= $newCode."\n";
        $_code .= str_repeat(' ', 12)."# END $mark DO NOT REMOVE THIS LINE\n\n";
        $_code .= str_repeat(' ', 12).$bottom;

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