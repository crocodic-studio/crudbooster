<?php namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\controllers\scaffolding\traits\Join;
use crocodicstudio\crudbooster\exceptions\CBValidationException;
use crocodicstudio\crudbooster\models\ColumnModel;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use crocodicstudio\crudbooster\controllers\scaffolding\traits\ColumnsRegister;
use crocodicstudio\crudbooster\controllers\traits\ControllerSetting;

class CBController extends Controller
{
    use ColumnsRegister, Join, ControllerSetting;

    private $assignmentData;

    public function __construct()
    {
        columnSingleton()->newColumns();
        $this->cbInit();
        $this->data['columns'] = columnSingleton()->getColumns();
        view()->share($this->data);
    }

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

    private function repository()
    {
        $joins = columnSingleton()->getJoin();
        $columns = columnSingleton()->getColumns();

        $query = DB::table($this->data['table']);


        $query->addSelect($this->data['table'].'.'.cb()->pk($this->data['table']).' as primary_key');

        if(isset($this->data['hook_query_index']) && is_callable($this->data['hook_query_index']))
        {
            $query = call_user_func($this->data['hook_query_index'], $query);
        }

        $softDelete = isset($this->data['disable_soft_delete'])?$this->data['disable_soft_delete']:true;
        if($softDelete === true && Schema::hasColumn($this->data['table'],'deleted_at')) {
            $query->whereNull($this->data['table'].'.deleted_at');
        }


        if(isset($joins)) {
            foreach($joins as $join)
            {
                $query->join($join['target_table'],
                        $join['target_table_primary'],
                    $join['operator'],
                    $join['source_table_foreign'],
                    $join['type']);
            }
        }

        foreach($columns as $column) {
            /** @var ColumnModel $column */
            if(strpos($column->getField(),".") === false) {
                $query->addSelect($this->data['table'].'.'.$column->getField());
            }else{
                $query->addSelect($column->getField());
            }

            $query = getTypeHook($column->getType())->query($query, $column);
        }

        if(request()->has('q'))
        {
            if(isset($this->data['hook_search_query'])) {
                $query = call_user_func($this->data['hook_search_query'], $query);
            }else{
                $query->where(function ($where) use ($columns) {
                    /**
                     * @var $where Builder
                     */
                    foreach($columns as $column)
                    {
                        $where->orWhere($column['name'], 'like', '%'.request('q').'%');
                    }
                });
            }
        }

        if(isset($this->data['hook_query_index']) && is_callable($this->data['hook_query_index'])) {
            $query = call_user_func($this->data['hook_query_index'], $query);
        }


        if(request()->has(['order_by','order_sort']))
        {
            if(in_array(request('order_by'),columnSingleton()->getColumnNameOnly())) {
                $query->orderBy(request('order_by'), request('order_sort'));
            }
        }else{
            if(isset($this->data['order_by'])) {
                $query->orderBy($this->data['order_by'][0], $this->data['order_by'][1]);
            }
        }

        return $query;
    }

    public function getIndex()
    {
        if(!module()->canBrowse()) return cb()->redirect(cb()->getAdminUrl(),"You do not have access to this area");

        $query = $this->repository();
        $result = $query->paginate(20);
        $data['result'] = $result;
        return view("crudbooster::module.index.index", $data);
    }


    /**
     * @throws CBValidationException
     */
    private function validation()
    {
        if(isset($this->data['validation'])) {
            $validator = Validator::make(request()->all(), @$this->data['validation'], @$this->data['validation_messages']);
            if ($validator->fails()) {
                $message = $validator->messages();
                $message_all = $message->all();
                throw new CBValidationException(implode(', ',$message_all));
            }
        }
    }

    public function getAdd()
    {
        if(!module()->canCreate()) return cb()->redirect(cb()->getAdminUrl(),"You do not have access to this area");

        $data = [];
        $data['page_title'] = $this->data['page_title'].' : Add';
        $data['action_url'] = module()->addSaveURL();
        return view('crudbooster::module.form.form',$data);
    }

    public function postAddSave()
    {
        if(!module()->canCreate()) return cb()->redirect(cb()->getAdminUrl(),"You do not have access to this area");

        try {
            $this->validation();
            columnSingleton()->valueAssignment();
            $data = columnSingleton()->getAssignmentData();

            if(Schema::hasColumn($this->data['table'], 'created_at')) {
                $data['created_at'] = date('Y-m-d H:i:s');
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
            return cb()->redirectBack($e->getMessage(),'warning');
        }

        if (request('submit') == trans('crudbooster.button_save_more')) {
            return cb()->redirect(module()->addURL(), trans("crudbooster.alert_add_data_success"), 'success');
        } else {
            return cb()->redirect(module()->url(), trans("crudbooster.alert_add_data_success"), 'success');
        }
    }

    public function getEdit($id)
    {
        if(!module()->canUpdate()) return cb()->redirect(cb()->getAdminUrl(),"You do not have access to this area");

        $data = [];
        $data['row'] = $this->repository()->where($this->data['table'].'.'.getPrimaryKey($this->data['table']), $id)->first();
        $data['page_title'] = $this->data['page_title'].' : Edit';
        $data['action_url'] = module()->editSaveURL($id);
        return view('crudbooster::module.form.form', $data);
    }

    public function postEditSave($id)
    {
        if(!module()->canUpdate()) return cb()->redirect(cb()->getAdminUrl(),"You do not have access to this area");

        try {
            $this->validation();
            columnSingleton()->valueAssignment();
            $data = columnSingleton()->getAssignmentData();
            if(Schema::hasColumn($this->data['table'], 'updated_at')) {
                $data['updated_at'] = date('Y-m-d H:i:s');
            }

            if(isset($this->data['hook_before_update']) && is_callable($this->data['hook_before_update'])) {
                call_user_func($this->data['hook_before_update'], $id);
            }

            DB::table($this->data['table'])
                ->where(cb()->pk($this->data['table']), $id)
                ->update($data);

            if(isset($this->data['hook_after_update']) && is_callable($this->data['hook_after_update'])) {
                call_user_func($this->data['hook_after_update'], $id);
            }

        } catch (CBValidationException $e) {
            Log::debug($e);
            return cb()->redirectBack($e->getMessage(),'info');
        } catch (\Exception $e) {
            Log::error($e);
            return cb()->redirectBack($e->getMessage(),'warning');
        }


        if (request('submit') == trans('crudbooster.button_save_more')) {
            return cb()->redirectBack(trans("crudbooster.alert_update_data_success"), 'success');
        } else {
            return cb()->redirect(module()->url(), trans("crudbooster.alert_update_data_success"), 'success');
        }
    }

    public function getDelete($id)
    {
        if(!module()->canDelete()) return cb()->redirect(cb()->getAdminUrl(),"You do not have access to this area");

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

        return cb()->redirectBack(trans("crudbooster.alert_delete_data_success"), 'success');
    }

    public function getDetail($id)
    {
        if(!module()->canRead()) return cb()->redirect(cb()->getAdminUrl(),"You do not have access to this area");

        $data = [];
        $data['row'] = $this->repository()->where($this->data['table'].'.'.getPrimaryKey($this->data['table']), $id)->first();
        $data['page_title'] = $this->data['page_title'].' : Detail';
        return view('crudbooster::module.form.form_detail', $data);
    }

    public function postUploadFile()
    {
        if(auth()->guest()) return redirect(cb()->getLoginUrl());

        $file = null;
        try {

            cb()->validation([
                'userfile' => 'required|mimes:' . config('crudbooster.UPLOAD_TYPES')
            ]);

            $file = cb()->uploadFile('userfile', true);

        } catch (CBValidationException $e) {
            return response()->json(['status'=>false,'message'=>$e->getMessage()]);
        } catch (\Exception $e) {
            return response()->json(['status'=>false,'message'=>$e->getMessage()]);
        }

        return response()->json([
            'status'=>true,
            'filename'=>basename($file),
            'full_url'=>asset($file),
            'url'=>$file
        ]);
    }
}
