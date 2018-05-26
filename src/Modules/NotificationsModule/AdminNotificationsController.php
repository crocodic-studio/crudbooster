<?php

namespace crocodicstudio\crudbooster\Modules\NotificationsModule;

use crocodicstudio\crudbooster\controllers\CBController;
use crocodicstudio\crudbooster\helpers\CRUDBooster;

class AdminNotificationsController extends CBController
{
    /**
     * AdminNotificationsController constructor.
     */
    public function __construct()
    {
        $this->table = "cms_notifications";
        $this->primaryKey = "id";
        $this->titleField = "content";
        $this->limit = 20;
        $this->orderby = ["id" => "desc"];
    }

    public function cbInit()
    {
        $this->setButtons();
        $this->makeColumns();

        $this->form = NotificationForm::makeForm();
    }

    private function setButtons()
    {
        $this->buttonShow = true;
        $this->buttonAdd = false;
        $this->deleteBtn = true;
        $this->buttonExport = false;
        $this->buttonImport = false;
    }

    private function makeColumns()
    {
        $read_notification_url = url(cbAdminPath()).'/notifications/read';

        $this->col = [
            [
                "label" => "Content",
                "name" => "content",
                "callback_php" => '"<a href=\"'.$read_notification_url.'/$row->id\">$row->content</a>"'
            ],
            [
                'label' => 'Read',
                'name' => 'is_read',
                'callback_php' => '($row->is_read)?"<span class=\"label label-default\">Already Read</span>":"<span class=\"label label-danger\">NEW</span>"',
            ],
        ];
    }

    public function hookQueryIndex($query)
    {
        $query->where('cms_users_id', auth('cbAdmin')->id());
    }

    public function getLatestJson()
    {
        $rows = $this->table()
            ->where('cms_users_id', 0)
            ->orWhere('cms_users_id', auth('cbAdmin')->id())
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
}