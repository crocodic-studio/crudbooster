<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 8/15/2019
 * Time: 11:44 PM
 */

namespace crocodicstudio\crudbooster\controllers\traits;

use Illuminate\Support\Facades\Validator;
use crocodicstudio\crudbooster\exceptions\CBValidationException;

trait Validation
{

    /**
     * @throws CBValidationException
     */
    private function validation()
    {
        if(isset($this->data['validation'])) {
            $validator = Validator::make(request()->all(), @$this->data['validation'], @$this->data['validation_messages']);
            if ($validator->fails()) {
                $message = $validator->messages();
                $message_all = $message->all();
                throw new CBValidationException(implode(', ',$message_all));
            }
        }
    }

}