<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 8/15/2019
 * Time: 11:44 PM
 */

namespace crocodicstudio\crudbooster\controllers\traits;

use crocodicstudio\crudbooster\helpers\SchemaHelper;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

trait Query
{

    private function repository($callback = null)
    {
        $joins = columnSingleton()->getJoin();
        $columns = columnSingleton()->getColumns();

        $query = DB::table($this->data['table']);

        $query->addSelect($this->data['table'].'.'.cb()->pk($this->data['table']).' as primary_key');

        $softDelete = isset($this->data['disable_soft_delete'])?$this->data['disable_soft_delete']:true;
        if($softDelete === true && SchemaHelper::hasColumn($this->data['table'],'deleted_at')) {
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
            if($column->getType() != "custom") {
                if(strpos($column->getField(),".") === false) {
                    if(SchemaHelper::hasColumn($this->data['table'], $column->getField())) {
                        $query->addSelect($this->data['table'].'.'.$column->getField());
                    }
                }else{
                    $query->addSelect($column->getField());
                }
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
                        if(strpos($column->getField(),".") === false) {
                            $field = $this->data['table'].'.'.$column->getField();
                        }else{
                            $field = $column->getField();
                        }
                        $where->orWhere($field, 'like', '%'.request('q').'%');
                    }
                });
            }
        }


        // Callback From this Method
        if(isset($callback) && is_callable($callback)) {
            $query = call_user_func($callback, $query);
        }

        if(isset($this->data['hook_index_query']) && is_callable($this->data['hook_index_query'])) {
            $query = call_user_func($this->data['hook_index_query'], $query);
        }


        if(request()->has(['order_by','order_sort']))
        {
            if(in_array(request('order_by'),columnSingleton()->getColumnNameOnly())) {
                $query->orderBy(request('order_by'), request('order_sort'));
            }
        }else{
            $query->orderBy($this->data['table'].'.'.cb()->findPrimaryKey($this->data['table']), "desc");
        }

        return $query;
    }

}