<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/25/2019
 * Time: 9:28 PM
 */

namespace crocodicstudio\crudbooster\controllers;


use crocodicstudio\crudbooster\exceptions\CBValidationException;
use Doctrine\DBAL\Query\QueryException;
use Illuminate\Support\Facades\DB;

class DeveloperMailController extends Controller
{
    private $view = "crudbooster::dev_layouts.modules.mail";

    public function __construct()
    {
        view()->share(['page_title'=>'Mail Configuration']);
    }

    public function getIndex() {
        $data = [];
        return view($this->view.".index",$data);
    }

    public function postSave()
    {
        setEnvironmentValue([
            "MAIL_DRIVER"=>request("MAIL_DRIVER"),
            "MAIL_HOST"=>request("MAIL_HOST"),
            "MAIL_PORT"=>request("MAIL_PORT"),
            "MAIL_USERNAME"=>request("MAIL_USERNAME"),
            "MAIL_PASSWORD"=>request("MAIL_PASSWORD"),
            "MAIL_ENCRYPTION"=>request("MAIL_ENCRYPTION")
        ]);

        return cb()->redirectBack("Mail configuration has been updated!","success");
    }
}