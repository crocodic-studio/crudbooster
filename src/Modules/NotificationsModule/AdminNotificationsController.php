<?php

namespace crocodicstudio\crudbooster\Modules\NotificationsModule;

use crocodicstudio\crudbooster\controllers\CBController;
use CRUDBooster;

class AdminNotificationsController extends CBController
{
    /**
     * AdminNotificationsController constructor.
     */
    public function __construct()
    {
        $this->table = "cms_notifications";
        $this->primaryKey = "id";
        $this->title_field = "content";
        $this->limit = 20;
        $this->index_orderby = ["id" => "desc"];
        $this->global_privilege = true;
    }

    public function cbInit()
    {
        $this->setButtons();
        $this->makeColumns();

        $this->form = NotificationForm::makeForm();
    }

    public function hookQueryIndex(&$query)
    {
        $query->where('id_cms_users', CRUDBooster::myId());
    }

    public function getLatestJson()
    {
        $rows = $this->table()
            ->where('id_cms_users', 0)
            ->orWhere('id_cms_users', CRUDBooster::myId())
            ->orderby('id', 'desc')
            ->where('is_read', 0)
            ->whereNull('deleted_at')
            ->take(25)
            ->get();

        return response()->json(['items' => $rows, 'total' => count($rows)]);
    }

    public function getRead($id)
    {
        $this->findRow($id)->update(['is_read' => 1]);
        $row = $this->findRow($id)->first();

        return redirect($row->url);
    }

    private function makeColumns()
    {
        $read_notification_url = url(cbAdminPath()).'/notifications/read';

        $this->col = [];
        $this->col[] = ["label" => "Content", "name" => "content", "callback_php" => '"<a href=\"'.$read_notification_url.'/$row->id\">$row->content</a>"'];
        $this->col[] = [
            'label' => 'Read',
            'name' => 'is_read',
            'callback_php' => '($row->is_read)?"<span class=\"label label-default\">Already Read</span>":"<span class=\"label label-danger\">NEW</span>"',
        ];
    }

    private function setButtons()
    {
        $this->button_show = true;
        $this->button_add = false;
        $this->deleteBtn = true;
        $this->buttonExport = false;
        $this->button_import = false;
    }
}