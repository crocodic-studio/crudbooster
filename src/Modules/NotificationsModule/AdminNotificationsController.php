<?php

namespace Crocodicstudio\Crudbooster\Modules\NotificationsModule;

use Crocodicstudio\Crudbooster\Controllers\CBController;
use CSSBootstrap;

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
        $this->buttonAdd = false;
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
                "callback"=>function($row) {
                    return cbAnchor($read_notification_url.'/'.$row->id,$row->content);
                }                
            ],
            [
                'label' => 'Read',
                'name' => 'can_read',
                "callback"=>function($row) {
                    if($row->can_read) {
                        return CSSBootstrap::label('Already Read','success');
                    }else{
                        return CSSBootstrap::label('New','danger');
                    }
                }                
            ],
        ];
    }

    public function hookQueryIndex(&$query)
    {
        $query->where('cms_users_id', auth('cbAdmin')->id());
    }

    public function getLatestJson()
    {
        $rows = $this->table()
            ->where('cms_users_id', 0)
            ->orWhere('cms_users_id', auth('cbAdmin')->id())
            ->orderby('id', 'desc')
            ->where('can_read', 0)
            ->whereNull('deleted_at')
            ->take(25)
            ->get();

        return response()->json(['items' => $rows, 'total' => count($rows)]);
    }

    public function getRead($id)
    {
        $this->findRow($id)->update(['can_read' => 1]);
        $row = $this->findRow($id)->first();

        return redirect($row->url);
    }
}