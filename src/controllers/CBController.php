<?php namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\controllers\scaffolding\traits\Join;
use crocodicstudio\crudbooster\controllers\traits\ColumnIntervention;
use crocodicstudio\crudbooster\controllers\traits\Query;
use crocodicstudio\crudbooster\controllers\traits\Validation;
use crocodicstudio\crudbooster\controllers\scaffolding\traits\ColumnsRegister;
use crocodicstudio\crudbooster\controllers\traits\ControllerSetting;
use crocodicstudio\crudbooster\exceptions\CBValidationException;
use crocodicstudio\crudbooster\models\ColumnModel;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CBController extends Controller
{
    use ColumnsRegister, Join, ControllerSetting, Validation, Query, SubModuleController, ColumnIntervention;

    private $assignmentData;

    public function __construct()
    {
        columnSingleton()->newColumns();
        $this->defaultData();
        $this->cbInit();
        $this->columnIntervention();
    }

    public function getIndex()
    {
        if(!module()->canBrowse()) return cb()->redirect(cb()->getAdminUrl(),cbLang("you_dont_have_privilege_to_this_area"));
        
        $query = $this->repository();
        $result = $query->paginate( request("limit")?:$this->data['limit'] );
        $data['result'] = $result;

        return view("crudbooster::module.index.index", array_merge($data, $this->data));
    }

    public function getAdd()
    {
        if(!module()->canCreate()) return cb()->redirect(cb()->getAdminUrl(),cbLang("you_dont_have_privilege_to_this_area"));

        $data = [];
        $data['page_title'] = $this->data['page_title'].' : '.cbLang('add');
        $data['action_url'] = module()->addSaveURL();
        return view('crudbooster::module.form.form',array_merge($data, $this->data));
    }

    public function postAddSave()
    {
        if(!module()->canCreate()) return cb()->redirect(cb()->getAdminUrl(),cbLang("you_dont_have_privilege_to_this_area"));

        try {
            $this->validation();
            columnSingleton()->valueAssignment();
            $data = columnSingleton()->getAssignmentData();

            //Clear data from Primary Key
            unset($data[ cb()->pk($this->data['table']) ]);

            if(Schema::hasColumn($this->data['table'], 'created_at')) {
                $data['created_at'] = date('Y-m-d H:i:s');
            }

            if(Schema::hasColumn($this->data['table'], 'updated_at')) {
                $data['updated_at'] = date('Y-m-d H:i:s');
            }

            if(isset($this->data['hook_before_insert']) && is_callable($this->data['hook_before_insert'])) {
                $data = call_user_func($this->data['hook_before_insert'], $data);
            }

            $id = DB::table($this->data['table'])->insertGetId($data);

            if(isset($this->data['hook_after_insert']) && is_callable($this->data['hook_after_insert'])) {
                call_user_func($this->data['hook_after_insert'], $id);
            }

        } catch (CBValidationException $e) {
            Log::debug($e);
            return cb()->redirectBack($e->getMessage(),'info');
        } catch (\Exception $e) {
            Log::error($e);
            return cb()->redirectBack(cbLang("something_went_wrong"),'warning');
        }

        if (Str::contains(request("submit"),cbLang("more"))) {
            return cb()->redirectBack(cbLang("the_data_has_been_added"), 'success');
        } else {
            if(verifyReferalUrl()) {
                return cb()->redirect(getReferalUrl("url"), cbLang("the_data_has_been_added"), 'success');
            } else {
                return cb()->redirect(module()->url(), cbLang("the_data_has_been_added"), 'success');
            }
        }
    }

    public function getEdit($id)
    {
        if(!module()->canUpdate()) return cb()->redirect(cb()->getAdminUrl(),cbLang("you_dont_have_privilege_to_this_area"));

        $data = [];
        $data['row'] = $this->repository()->where($this->data['table'].'.'.getPrimaryKey($this->data['table']), $id)->first();
        $data['page_title'] = $this->data['page_title'].' : '.cbLang('edit');
        $data['action_url'] = module()->editSaveURL($id);
        return view('crudbooster::module.form.form', array_merge($data, $this->data));
    }

    public function postEditSave($id)
    {
        if(!module()->canUpdate()) return cb()->redirect(cb()->getAdminUrl(),cbLang("you_dont_have_privilege_to_this_area"));

        try {
            $this->validation();
            columnSingleton()->valueAssignment();
            $data = columnSingleton()->getAssignmentData();

            // Make sure the Primary Key is not included
            unset($data[ cb()->pk($this->data['table']) ]);

            if(Schema::hasColumn($this->data['table'], 'updated_at')) {
                $data['updated_at'] = date('Y-m-d H:i:s');
            }

            unset($data['created_at']);

            if(isset($this->data['hook_before_update']) && is_callable($this->data['hook_before_update'])) {
                $data = call_user_func($this->data['hook_before_update'], $data, $id);
            }

            cb()->update($this->data['table'], $id, $data);

            if(isset($this->data['hook_after_update']) && is_callable($this->data['hook_after_update'])) {
                call_user_func($this->data['hook_after_update'], $id);
            }

        } catch (CBValidationException $e) {
            Log::debug($e);
            return cb()->redirectBack($e->getMessage(),'info');
        } catch (\Exception $e) {
            Log::error($e);
            return cb()->redirectBack(cbLang("something_went_wrong"),'warning');
        }


        if (Str::contains(request("submit"), cbLang("more"))) {
            return cb()->redirectBack( cbLang("the_data_has_been_updated"), 'success');
        } else {
            if(verifyReferalUrl()) {
                return cb()->redirect(getReferalUrl("url"),  cbLang("the_data_has_been_updated"), 'success');
            } else {
                return cb()->redirect(module()->url(),  cbLang("the_data_has_been_updated"), 'success');
            }

        }
    }

    public function getDelete($id)
    {
        if(!module()->canDelete()) return cb()->redirect(cb()->getAdminUrl(),cbLang("you_dont_have_privilege_to_this_area"));

        if(isset($this->data['hook_before_delete']) && is_callable($this->data['hook_before_delete'])) {
            call_user_func($this->data['hook_before_delete'], $id);
        }

        $softDelete = isset($this->data['disable_soft_delete'])?$this->data['disable_soft_delete']:true;

        if ($softDelete === true && Schema::hasColumn($this->data['table'],'deleted_at')) {
            DB::table($this->data['table'])
                ->where(getPrimaryKey($this->data['table']), $id)
                ->update(['deleted_at' => date('Y-m-d H:i:s')]);
        } else {
            DB::table($this->data['table'])
                ->where(getPrimaryKey($this->data['table']), $id)
                ->delete();
        }

        if(isset($this->data['hook_after_delete']) && is_callable($this->data['hook_after_delete'])) {
            call_user_func($this->data['hook_after_delete'], $id);
        }

        return cb()->redirectBack( cbLang("the_data_has_been_deleted"), 'success');
    }

    public function getDetail($id)
    {
        if(!module()->canRead()) return cb()->redirect(cb()->getAdminUrl(),cbLang("you_dont_have_privilege_to_this_area"));

        $data = [];
        $data['row'] = $this->repository()->where($this->data['table'].'.'.cb()->findPrimaryKey($this->data['table']), $id)->first();
        $data['page_title'] = $this->data['page_title'].' : '.cbLang('detail');
        return view('crudbooster::module.form.form_detail', array_merge($data, $this->data));
    }



    /**
     * @param string $method
     * @param array $parameters
     * @return mixed|null
     *
     * This method is to get the data columns and retrieve the value from this class
     */
    public function __call($method, $parameters)
    {
        if($method == "getData") {
            $key = $parameters[0];
            if(isset($this->data[$key])) {
                return $this->data[$key];
            }else{
                return null;
            }
        }else{
            return null;
        }
    }
}
