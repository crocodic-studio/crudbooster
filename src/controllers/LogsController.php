<?php namespace crocodicstudio\crudbooster\controllers;

use Illuminate\Support\Facades\Excel;
use Illuminate\Support\Facades\PDF;

class LogsController extends CBController
{
    public function cbInit()
    {
        $this->table = 'cms_logs';
        $this->primary_key = 'id';
        $this->title_field = "ipaddress";
        $this->button_bulk_action = true;
        $this->button_export = false;
        $this->button_import = false;
        $this->button_add = false;
        $this->button_edit = false;
        $this->button_delete = true;

        $this->col = [];
        $this->col[] = ["label" => "Time Access", "name" => "created_at"];
        $this->col[] = ["label" => "IP Address", "name" => "ipaddress"];
        $this->col[] = ["label" => "User", "name" => "id_cms_users", "join" => config('crudbooster.USER_TABLE').",name"];
        $this->col[] = ["label" => "Description", "name" => "description"];

        $this->form = [];
        $this->form[] = ["label" => "Time Access", "name" => "created_at", "readonly" => true];
        $this->form[] = ["label" => "IP Address", "name" => "ipaddress", "readonly" => true];
        $this->form[] = ["label" => "User Agent", "name" => "useragent", "readonly" => true];
        $this->form[] = ["label" => "URL", "name" => "url", "readonly" => true];
        $this->form[] = [
            "label" => "User",
            "name" => "id_cms_users",
            "type" => "select",
            "datatable" => config('crudbooster.USER_TABLE').",name",
            "readonly" => true,
        ];
        $this->form[] = ["label" => "Description", "name" => "description", "readonly" => true];
        $this->form[] = ["label" => "Details", "name" => "details", "type" => "custom"];
    }

    public static function displayDiff($old_values, $new_values)
    {
        $diff = self::getDiff($old_values, $new_values);
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
