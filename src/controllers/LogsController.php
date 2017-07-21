<?php namespace crocodicstudio\crudbooster\controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Excel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\PDF;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LogsController extends CBController
{
	public function cbInit()
	{
		$this->table              = 'cms_logs';
		$this->primary_key        = 'id';
		$this->title_field        = "ipaddress";
		$this->button_bulk_action = true;
		$this->button_export      = false;
		$this->button_import      = false;
		$this->button_add         = false;
		$this->button_edit        = false;
		$this->button_delete      = true;

		$this->col   = [];
		$this->col[] = ["label" => "Time Access", "name" => "created_at"];
		$this->col[] = ["label" => "IP Address", "name" => "ipaddress"];
		$this->col[] = ["label" => "User", "name" => "id_cms_users", "join" => "cms_users,name"];
		$this->col[] = ["label" => "Description", "name" => "description"];

		$this->form   = [];
		$this->form[] = ["label" => "Time Access", "name" => "created_at", "readonly" => true];
		$this->form[] = ["label" => "IP Address", "name" => "ipaddress", "readonly" => true];
		$this->form[] = ["label" => "User Agent", "name" => "useragent", "readonly" => true];
		$this->form[] = ["label" => "URL", "name" => "url", "readonly" => true];
		$this->form[] = ["label"     => "User", "name"      => "id_cms_users", "type"      => "select", "datatable" => "cms_users,name", "readonly"  => true,];
		$this->form[] = ["label" => "Description", "name" => "description", "readonly" => true];
		$this->form[] = ["label" => "Details", "name" => "details", 'type' => 'custom'];
	}
}
