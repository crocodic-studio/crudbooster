<?php namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\PDF;
use Illuminate\Support\Facades\Excel;

class LogsController extends CBController {

	public function cbInit() {
		$this->table         = 'cms_logs';
		$this->primary_key   = 'id';
		$this->title_field   = "ipaddress";
		$this->button_bulk_action = true;
		$this->button_export = false;
		$this->button_import = false;
		$this->button_add    = false;
		$this->button_edit   = false;
		$this->button_delete = true;

		$this->col = array();
		$this->col[] = array("label"=>"Time Access","name"=>"created_at");
		$this->col[] = array("label"=>"IP Address","name"=>"ipaddress");
        $this->col[] = array("label"=>"User","name"=>"id_cms_users","join"=>config('crudbooster.USER_TABLE').",name");
		$this->col[] = array("label"=>"Description","name"=>"description");

		$this->form = array();
		$this->form[] = array("label"=>"Time Access","name"=>"created_at","readonly"=>true);
		$this->form[] = array("label"=>"IP Address","name"=>"ipaddress","readonly"=>true);
		$this->form[] = array("label"=>"User Agent","name"=>"useragent","readonly"=>true);
		$this->form[] = array("label"=>"URL","name"=>"url","readonly"=>true);
        $this->form[] = array("label"=>"User","name"=>"id_cms_users","type"=>"select","datatable"=>config('crudbooster.USER_TABLE').",name","readonly"=>true);
		$this->form[] = array("label"=>"Description","name"=>"description","readonly"=>true);
		$this->form[] = array("label"=>"Details","name"=>"details","type"=>"custom");

	}

	public static function displayDiff($old_values, $new_values)
	{
		$diff  = self::getDiff($old_values, $new_values);
		$table = '<table class="table table-striped"><thead><tr><th>Key</th><th>Old Value</th><th>New Value</th></thead><tbody>';
		foreach ($diff as $key => $value) {
			$table .= "<tr><td>$key</td><td>$old_values[$key]</td><td>$new_values[$key]</td></tr>";
		}
		$table .= '</tbody></table>';

		return $table;
	}

	private static function getDiff($old_values, $new_values)
	{
		unset($old_values['id']);
		unset($old_values['created_at']);
		unset($old_values['updated_at']);
		unset($new_values['created_at']);
		unset($new_values['updated_at']);
		return array_diff($old_values, $new_values);
	}
}
