<?php

namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\exceptions\CBValidationException;
use crocodicstudio\crudbooster\helpers\Plugin;
use Doctrine\DBAL\Query\QueryException;
use Illuminate\Support\Facades\DB;

class DeveloperThemesController extends Controller
{
    private $view = "crudbooster::dev_layouts.modules.themes";

    public function __construct()
    {
        view()->share(['page_title'=>'Themes']);
    }

    public function getIndex() {
        $themes = (new Plugin())->getAllThemes();
        $data = [];
        $data['result'] = $themes;
        return view($this->view.".index",$data);
    }

    public function getActiveTheme($theme_path) {
        $theme_path = base64_decode($theme_path);
        putSetting("theme_path", $theme_path);
        return cb()->redirectBack("Theme has been activated!","success");
    }

    public function postSaveConfig() {

        foreach(request()->except("_token") as $key=>$val) {
            putSetting( $key,  $val);
        }

        return cb()->redirectBack("Theme config has been saved!","success");
    }
}