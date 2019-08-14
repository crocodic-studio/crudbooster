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

class DeveloperMiscellaneousController extends Controller
{
    private $view = "crudbooster::dev_layouts.modules.miscellaneous";

    public function __construct()
    {
        view()->share(['page_title'=>'Miscellaneous']);
    }

    public function getIndex() {
        $data = [];
        return view($this->view.".index",$data);
    }

    public function postSave()
    {
        foreach(request()->except("_token") as $key=>$val) {
            putSetting( $key,  $val);
        }

        return cb()->redirectBack("Setting has been updated!","success");
    }
}