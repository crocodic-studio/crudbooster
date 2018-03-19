<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator\ControllerGenerator;

class FieldDetector
{
    /**
     * @param $field
     * @return bool
     */
    static function isPassword($field)
    {
        return in_array($field, explode(',', cbConfig('PASSWORD_FIELDS_CANDIDATE')));
    }

    /**
     * @param $field
     * @return bool
     */
    static function isEmail($field)
    {
        return in_array($field, explode(',', cbConfig('EMAIL_FIELDS_CANDIDATE')));
    }

    /**
     * @param $field
     * @return bool
     */
    static function isPhone($field)
    {
        return in_array($field, explode(',', cbConfig('PHONE_FIELDS_CANDIDATE')));
    }

    /**
     * @param $field
     * @return bool
     */
    static function isImage($field)
    {
        return in_array($field, explode(',', cbConfig('IMAGE_FIELDS_CANDIDATE')));
    }

    /**
     * @param $field
     * @return bool
     */
    static function isExceptional($field)
    {
        return in_array($field, ['id', 'created_at', 'updated_at', 'deleted_at']);
    }

    /**
     * @param $field
     * @return bool
     */
    static function isForeignKey($field)
    {
        return substr($field, 0, 3) == 'id_' || substr($field, -3) == '_id';
    }

    /**
     * @param $field
     * @return bool
     */
    static function isGeographical($field)
    {
        return in_array($field, ['latitude', 'longitude']);
    }

    /**
     * @param $field
     * @return bool
     */
    static function isNameField($field)
    {
        return in_array($field, explode(',', cbConfig('NAME_FIELDS_CANDIDATE')));
    }

    /**
     * @param $field
     * @return bool
     */
    static function isUrlField($field)
    {
        return in_array($field, explode(',', cbConfig("URL_FIELDS_CANDIDATE")));
    }
}