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

class DeveloperSecurityController extends Controller
{
    private $view = "crudbooster::dev_layouts.modules.security";

    public function __construct()
    {
        view()->share(['page_title'=>'Security']);
    }

    public function getIndex() {
        $data = [];
        return view($this->view.".index",$data);
    }

    public function postSave()
    {
        if(!isConfigCached()) {
            setEnvironmentValue([
                "APP_DEBUG"=>request("APP_DEBUG")
            ]);
        }

        putSetting("ADMIN_PATH", request("ADMIN_PATH"));
        putSetting("DISABLE_LOGIN", request("DISABLE_LOGIN"));
        putSetting("AUTO_SUSPEND_LOGIN", request("AUTO_SUSPEND_LOGIN"));
        putSetting("AUTO_REDIRECT_TO_LOGIN", request("AUTO_REDIRECT_TO_LOGIN"));
        putSetting("LOGIN_NOTIFICATION", request("LOGIN_NOTIFICATION"));

        if(request("htaccess_ServerSignature")) {
            putHtaccess("ServerSignature Off");
        }

        if(request("htaccess_IndexIgnore")) {
            putHtaccess("IndexIgnore *");
        }

        if(request("htaccess_dotAccess")) {
putHtaccess("
<FilesMatch \"^\.\">
Order allow,deny
Deny from all
</FilesMatch>");
        }

        if(request("htaccess_preventVendor")) {
            putHtaccess("RewriteRule ^(.*)/vendor/.*\.(php|rb|py)$ - [F,L,NC]");
        }


        return cb()->redirectBack("Security has been updated!","success");
    }
}