<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator;

class ControllerConfigParser
{
    public static function parse($code)
    {
        $configCode = ScaffoldingParser::extract_unit($code, cbStartMarker('CONFIGURATION'), cbEndMarker('CONFIGURATION'));
        $configCode = preg_replace('/[\t\n\r\0\x0B]/', '', $configCode);
        $configCode = preg_replace('/([\s])\1+/', ' ', $configCode);
        $configCode = explode(";", $configCode);
        $configCode = array_map('trim', $configCode);
        $result = self::toArray($code, $configCode);

        return $result;
    }

    /**
     * @param $code
     * @param $configCode
     * @return array
     */
    private static function toArray($code, $configCode)
    {
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