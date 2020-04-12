<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/7/2020
 * Time: 5:57 PM
 */

namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\helpers\addActionButton;
use crocodicstudio\crudbooster\inputs\InputContainer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

trait CBControllerHelper
{

    public function addActionButton($label, $url, $icon = "fa fa-bars", $color = "primary") {
        return (new addActionButton($label, $url, $icon, $color));
    }

    private function verifyInputInterface() {
        if(cb()->currentMethodIs(['getAdd','getEdit','postAddSave','postEditSave','getDetail'])) {
            foreach ($this->form as $i=>$form) {
                if($form instanceof InputContainer) {
                    $this->form[$i] = $form->toArray();
                }
            }
        }
    }

    private function sidebarModeTrans($sidebar_mode) {
        if ($sidebar_mode == 'mini') {
            $sidebar_mode = 'sidebar-mini';
        } elseif ($sidebar_mode == 'collapse') {
            $sidebar_mode = 'sidebar-collapse';
        } elseif ($sidebar_mode == 'collapse-mini') {
            $sidebar_mode = 'sidebar-collapse sidebar-mini';
        } else {
            $sidebar_mode = '';
        }
        return $sidebar_mode;
    }

    private function parentModuleCondition(&$data) {
        if (request()->get('parent_table')) {
            $parentTablePK = cb()->pk(g('parent_table'));
            $data['parent_table'] = DB::table(request()->get('parent_table'))->where($parentTablePK, request()->get('parent_id'))->first();
            if (request()->get('foreign_key')) {
                $data['parent_field'] = request()->get('foreign_key');
            } else {
                $data['parent_field'] = cb()->getTableForeignKey(g('parent_table'), $this->table);
            }

            if ($parent_field) {
                foreach ($this->columns_table as $i => $col) {
                    if ($col['name'] == $parent_field) {
                        unset($this->columns_table[$i]);
                    }
                }
            }
        }
    }
}