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
use crocodicstudio\crudbooster\models\ColumnModel;

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

            // Query From Type
            $query = getTypeHook($column->getType())->query($query, $column);

            // Filter Query From Type
            $filterValue = request("filter_".slug($column->getFilterColumn(),"_"));
            if(is_array($filterValue) || sanitizeXSS($filterValue)) {
                $query = getTypeHook($column->getType())->filterQuery($query, $column, $filterValue);
            }
        }

        if(request()->has('q'))
        {
            $keyword = sanitizeXSS(request("q"));
            if(isset($this->data['hook_search_query'])) {
                $query = call_user_func($this->data['hook_search_query'], $query, $keyword);
            }else{
                $query->where(function ($where) use ($columns, $keyword) {
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
                        $where->orWhere($field, 'like', '%'.$keyword.'%');
                    }
                });
            }
        }


        /*
         * This script to hanlde the callback inputed on this query method
         */
        if(isset($callback) && is_callable($callback)) {
            $query = call_user_func($callback, $query);
        }

        if(isset($this->data['hook_index_query']) && is_callable($this->data['hook_index_query'])) {
            $query = call_user_func($this->data['hook_index_query'], $query);
        }


        if(request()->has(['order_by','order_sort']))
        {
            $sort = (request("order_sort")=="asc")?"asc":"desc";
            /*
             * Check if order by contain "." it means {table}.{column} we have to parsing that
             */
            if(strpos(request("order_by"), ".")!==false) {
                $orderByTable = explode(".",request("order_by"))[0];
                $orderByColumn = explode(".",request("order_by"))[1];
                if(SchemaHelper::hasColumn($orderByTable, $orderByColumn)) {
                    $query->orderBy($orderByTable.".".$orderByColumn, $sort);
                }
            } else {
                /*
                 * Check if order_by column in registered columns
                 */
                if(in_array(request('order_by'),columnSingleton()->getColumnNameOnly())) {
                    $query->orderBy(request('order_by'), request('order_sort'));
                }
            }
        }else{
            /*
             * For default, query will be order by primary key as descending
             */
            $query->orderBy($this->data['table'].'.'.cb()->findPrimaryKey($this->data['table']), "desc");
        }

        return $query;
    }

}