<?php

namespace crocodicstudio\crudbooster\controllers\ApiController;

class ApiValidations
{
    /**
     * @param $rowApi
     * @param $ctrl
     * @return mixed
     */
    public static function checkApiDefined($rowApi, $ctrl)
    {
        if (!is_null($rowApi)) {
            return true;
        }
        //todo : translation
        $msg = 'Sorry this API is no longer available, maybe has changed by admin, or please make sure api url is correct.';
        $result = ApiResponder::makeResult(0, $msg);
        ApiResponder::send($result, request()->all(), $ctrl);
    }


    /**
     * @param $methodType
     * @param $ctrl
     * @return mixed
     */
    public static function validateMethodType($methodType, $ctrl)
    {
        if (!is_null($methodType) && request()->isMethod($methodType)) {
            return true;
        }
        //todo : translation
        $result = ApiResponder::makeResult(0, "The request method is not allowed !");
        ApiResponder::send($result, request()->all(), $ctrl);
    }

    /**
     * @param $ctrl
     * @return mixed
     */
    public static function doCustomPreCheck($ctrl)
    {
        $ctrl->hookValidate();

        if (! $ctrl->validate) {
            return true;
        }  // hook have to return true
        //todo : translation
        $result = ApiResponder::makeResult(0, 'Failed to execute API !');
        ApiResponder::send($result, request()->all(), $ctrl);
    }


    /**
     * @param $rowApi
     * @param $ctrl
     */
    public static function doValidations($rowApi, $ctrl)
    {
        /* Check the row is exists or not */
        self::checkApiDefined($rowApi, $ctrl);

        /* Method Type validation */
        self::validateMethodType($rowApi->method_type, $ctrl);

        /* Do some custom pre-checking for posted data, if failed discard API execution */
        self::doCustomPreCheck($ctrl);
    }
}