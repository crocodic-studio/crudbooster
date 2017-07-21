<?php namespace crocodicstudio\crudbooster\controllers;

use CRUDBooster;
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

class NotificationsController extends CBController
{
	public function cbInit()
	{
		$this->table            = "cms_notifications";
		$this->primary_key      = "id";
		$this->title_field      = "content";
		$this->limit            = 20;
		$this->index_orderby    = ["id" => "desc"];
		$this->button_show      = true;
		$this->button_add       = false;
		$this->button_delete    = true;
		$this->button_export    = false;
		$this->button_import    = false;
		$this->global_privilege = true;

		$read_notification_url = url(config('crudbooster.ADMIN_PATH')) . '/notifications/read';

		$this->col   = [];
		$this->col[] = [
			"label"        => "Content",
			"name"         => "content",
			"callback_php" => '"<a href=\"' . $read_notification_url . '/$row->id\">$row->content</a>"',
		];
		$this->col[] = [
			'label'        => 'Read',
			'name'         => 'is_read',
			'callback_php' => '($row->is_read)?"<span class=\"label label-default\">Already Read</span>":"<span class=\"label label-danger\">NEW</span>"',
		];

		$this->form   = [];
		$this->form[] = ["label" => "Content", "name" => "content", "type" => "text"];
		$this->form[] = ["label" => "Icon", "name" => "icon", "type" => "text"];
		$this->form[] = ["label" => "Notification Command", "name" => "notification_command", "type" => "textarea"];
		$this->form[] = ["label" => "Is Read", "name" => "is_read", "type" => "text"];
	}

	function hook_query_index(&$query)
	{
		$query->where('id_cms_users', CRUDBooster::myId());
	}

	public function getLatestJson()
	{

		$rows = DB::table('cms_notifications')
		          ->where('id_cms_users', 0)
		          ->orWhere('id_cms_users', CRUDBooster::myId())
		          ->orderby('id', 'desc')
		          ->where('is_read', 0)
		          ->whereNull('deleted_at')
		          ->take(25)->get();

		$total = count($rows);

		return response()->json(['items' => $rows, 'total' => $total]);
	}

	public function getRead($id)
	{
		DB::table('cms_notifications')->where('id', $id)->update(['is_read' => 1]);
		$row = DB::table('cms_notifications')->where('id', $id)->first();

		return redirect($row->url);
	}
}