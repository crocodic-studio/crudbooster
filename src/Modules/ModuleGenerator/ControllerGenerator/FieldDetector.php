<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator\ControllerGenerator;

class FieldDetector
{
    /**
     * @param $fieldName string
     * @return bool
     */
    static function isExceptional($fieldName)
    {
        return in_array($fieldName, ['id', 'created_at', 'updated_at', 'deleted_at']);
    }

    /**
     * @param $fieldName string
     * @return bool
     */
    static function isForeignKey($fieldName)
    {
        return starts_with($fieldName, 'id_') || ends_with($fieldName, '_id');
    }

    static function isUploadField($fieldName)
    {
        return self::isWithin($fieldName, 'UPLOAD_TYPES');
    }

    static function isWithin($fieldName, $configKey)
    {
        return in_array($fieldName, explode(',', cbConfig($configKey)));
    }

    static function detect($colName)
    {
        $map = [
            'isPassword',
            'isImage',
            'isGeographical',
            'isPhone',
            'isEmail',
            'isNameField',
            'isUrlField',
        ];
        foreach ($map as $methodName){
            if (self::$methodName($colName)) {
                return $methodName;
            }
        }
        return 'not found';
    }

    /**
     * @param $fieldName string
     * @return bool
     */
    static function isPassword($fieldName)
    {
        return self::isWithin($fieldName, 'PASSWORD_FIELDS_CANDIDATE');
    }

    /**
     * @param $fieldName string
     * @return bool
     */
    static function isImage($fieldName)
    {
        return self::isWithin($fieldName, 'IMAGE_FIELDS_CANDIDATE');
    }

    /**
     * @param $fieldName string
     * @return bool
     */
    static function isGeographical($fieldName)
    {
        return in_array($fieldName, ['latitude', 'longitude']);
    }

    /**
     * @param $fieldName string
     * @return bool
     */
    static function isPhone($fieldName)
    {
        return self::isWithin($fieldName, 'PHONE_FIELDS_CANDIDATE');
    }

    /**
     * @param $fieldName string
     * @return bool
     */
    static function isEmail($fieldName)
    {
        return self::isWithin($fieldName, 'EMAIL_FIELDS_CANDIDATE');
    }

    /**
     * @param $fieldName string
     * @return bool
     */
    static function isNameField($fieldName)
    {
        return self::isWithin($fieldName, 'NAME_FIELDS_CANDIDATE');
    }

    /**
     * @param $fieldName string
     * @return bool
     */
    static function isUrlField($fieldName)
    {
        return self::isWithin($fieldName, 'URL_FIELDS_CANDIDATE');
    }
}