<?php namespace crocodicstudio\crudbooster\controllers;

use CRUDBooster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Excel;
use Illuminate\Support\Facades\PDF;

class NotificationsController extends CBController
{
    public function cbInit()
    {
        $this->table = "cms_notifications";
        $this->primary_key = "id";
        $this->title_field = "content";
        $this->limit = 20;
        $this->index_orderby = ["id" => "desc"];
        $this->button_show = true;
        $this->button_add = false;
        $this->button_delete = true;
        $this->button_export = false;
        $this->button_import = false;
        $this->global_privilege = true;

        $read_notification_url = url(config('crudbooster.ADMIN_PATH')).'/notifications/read';

        $this->col = [];
        $this->col[] = ["label" => "Content", "name" => "content", "callback_php" => '"<a href=\"'.$read_notification_url.'/$row->id\">$row->content</a>"'];
        $this->col[] = [
            'label' => 'Read',
            'name' => 'is_read',
            'callback_php' => '($row->is_read)?"<span class=\"label label-default\">Already Read</span>":"<span class=\"label label-danger\">NEW</span>"',
        ];

        $this->form = [];
        $this->form[] = ["label" => "Content", "name" => "content", "type" => "text"];
        $this->form[] = ["label" => "Icon", "name" => "icon", "type" => "text"];
        $this->form[] = ["label" => "Notification Command", "name" => "notification_command", "type" => "textarea"];
        $this->form[] = ["label" => "Is Read", "name" => "is_read", "type" => "text"];
    }

    public function hook_query_index(&$query)
    {
        $query->where('id_cms_users', CRUDBooster::myId());
    }

    public function getLatestJson()
    {

        $rows = DB::table('cms_notifications')->where('id_cms_users', 0)->orWhere('id_cms_users', CRUDBooster::myId())->orderby('id', 'desc')->where('is_read', 0)->take(25);
        if (\Schema::hasColumn('cms_notifications', 'deleted_at')) {
            $rows->whereNull('deleted_at');
        }

        $total = count($rows->get());

        return response()->json(['items' => $rows->get(), 'total' => $total]);
    }

    public function getRead($id)
    {
        DB::table('cms_notifications')->where('id', $id)->update(['is_read' => 1]);
        $row = DB::table('cms_notifications')->where('id', $id)->first();

        return redirect($row->url);
    }
}