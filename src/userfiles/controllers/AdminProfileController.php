<?php namespace App\Http\Controllers;

use crocodicstudio\crudbooster\controllers\ProfileController;
use crocodicstudio\crudbooster\models\CmsUsers;
use Illuminate\Support\Facades\Hash;
use Session;
use Request;
use DB;
use Illuminate\Routing\Controller as BaseController;

class AdminProfileController extends BaseController {

    use ProfileController;
}
