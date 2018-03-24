<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator;

class FileManipulator
{
    static function readMethodContent($code, $functionToFind)
    {
        $codeArray = explode("\n", $code);
        $tagBuka = 0;
        $tagPentutups = [];
        $methodIndex = [];
        foreach ($codeArray as $i => &$line) {
            if (strpos($line, 'function '.$functionToFind.'(') !== false) {
                $tagBuka = $i;
            }
            $tagPentutups = self::tagPentutups($line, $i, $tagPentutups);
            $methodIndex = self::methodIndex($line, $i, $methodIndex);
        }

        list($methodIndex, $methodNextIndex) = self::tagBuka($methodIndex, $tagBuka);

        $tagPentutups = array_values($tagPentutups);

        $finalTagPenutup = self::finalTagPenutup($tagBuka, $tagPentutups, $methodIndex, $methodNextIndex);

        $content = self::getContents($codeArray, $tagBuka, $finalTagPenutup);

        return implode("\n", $content);
    }


    static function writeMethodContent($code, $functionToFind, $stringToInsert)
    {
        $codeArray = explode("\n", $code);
        $tagBuka = 0;
        $tagPentutups = [];
        $methodIndex = [];
        $indentCount = 0;

        foreach ($codeArray as $i => &$line) {
            if (strpos($line, 'function '.$functionToFind.'(') !== false) {
                $tagBuka = $i;
                $indentCount = substr_count($line, "    ");
            }

            $tagPentutups = self::tagPentutups($line, $i, $tagPentutups);
            $methodIndex = self::methodIndex($line, $i, $methodIndex);
        }

        list($methodIndex, $methodNextIndex) = self::tagBuka($methodIndex, $tagBuka);

        $tagPentutups = array_values($tagPentutups);

        $finalTagPenutup = self::finalTagPenutup($tagBuka, $tagPentutups, $methodIndex, $methodNextIndex);

        //Removing Content Of Method
        $codeArray = self::removeMethodContent($codeArray, $tagBuka, $finalTagPenutup);

        //Insert Content To Method
        $stringToInsertArray = explode("\n", trim($stringToInsert));
        foreach ($stringToInsertArray as $i => &$s) {
            $s = str_repeat('    ', $indentCount + 2).trim($s);
        }

        $stringToInsert = implode("\n", $stringToInsertArray);
        foreach ($codeArray as $i => $c) {
            if ($i == $tagBuka) {
                array_splice($codeArray, $i + 1, 0, [$stringToInsert]);
            }
        }

        return implode("\n", $codeArray);
    }

    /**
     * @param $codeArray
     * @param $tagBuka
     * @param $finalTagPenutup
     * @return array
     */
    private static function getContents($codeArray, $tagBuka, $finalTagPenutup)
    {
        $content = [];
        foreach ($codeArray as $i => $line) {
            if ($i > $tagBuka && $i < $finalTagPenutup) {
                $content[] = $line;
            }
        }

        return $content;
    }

    /**
     * @param $tagBuka
     * @param $tagPentutups
     * @param $methodIndex
     * @param $methodNextIndex
     * @return mixed
     */
    private static function finalTagPenutup($tagBuka, $tagPentutups, $methodIndex, $methodNextIndex)
    {
        if ($tagBuka == end($methodIndex)) {
            return $tagPentutups[count($tagPentutups) - 2];
        }

        foreach ($tagPentutups as $i => $tp) {
            if ($tp > $methodIndex[$methodNextIndex]) {
                $finalTagPenutup = $tagPentutups[$i - 1];
                break;
            }
        }


        return $finalTagPenutup;
    }

    /**
     * @param $line
     * @param $e
     * @param $tagPentutups
     * @return mixed
     */
    private static function tagPentutups($line, $e, $tagPentutups)
    {
        if (stripos($line, '}') !== false) {
            $tagPentutups[$e] = $e;
        }

        if (stripos($line, '{') !== false) {
            $tagPembukas[$e] = $e;
        }

        return $tagPentutups;
    }

    /**
     * @param $line
     * @param $i
     * @param $methodIndex
     * @return array
     */
    private static function methodIndex($line, $i, $methodIndex)
    {
        foreach (['public', 'private'] as $m) {
            if (strpos($line, $m) !== false) {
                $methodIndex[] = $i;
                break;
            }
        }

        if (strpos($line, ' function ') !== false) {
            $methodIndex[] = $i;
        }

        return $methodIndex;
    }

    /**
     * @param $methodIndex
     * @param $tagBuka
     * @return array
     */
    private static function tagBuka($methodIndex, $tagBuka)
    {
        $methodIndex = array_values(array_unique($methodIndex));

        $keyTagBukaInMethodIndex = array_search($tagBuka, $methodIndex);
        $totalMethodIndex = count($methodIndex) - 1;
        $methodNextIndex = ($totalMethodIndex == $keyTagBukaInMethodIndex) ? $keyTagBukaInMethodIndex : $keyTagBukaInMethodIndex + 1;

        return [$methodIndex, $methodNextIndex];
    }

    /**
     * @param $codeArray array
     * @param $tagBuka int
     * @param $finalTagPenutup int
     * @return array
     */
    private static function removeMethodContent($codeArray, $tagBuka, $finalTagPenutup)
    {
        foreach ($codeArray as $i => $c) {
            if ($i > $tagBuka && $i < $finalTagPenutup) {
                unset($codeArray[$i]);
            }
        }
        return $codeArray;
    }

    static function putCtrlContent($ctrl, $fileContent)
    {
        return file_put_contents(controller_path($ctrl), $fileContent);
    }

    static function readCtrlContent($ctrl)
    {
        return file_get_contents(controller_path($ctrl));
    }
}