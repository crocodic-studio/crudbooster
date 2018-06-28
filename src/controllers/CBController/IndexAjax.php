<?php

namespace Crocodicstudio\Crudbooster\controllers\CBController;

use Crocodicstudio\Crudbooster\CBCoreModule\Search;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

trait IndexAjax
{
    public function getDataTable()
    {
        $table = request('table');
        $label = request('label');
        $datatableWhere = urldecode(request('datatable_where'));
        $foreignKeyName = request('fk_name');
        $foreignKeyValue = request('fk_value');
        if (! $table || ! $label || ! $foreignKeyName || ! $foreignKeyValue) {
            return response()->json([]);
        }
        $query = DB::table($table);
        if ($datatableWhere) {
            $query->whereRaw($datatableWhere);
        }
        $query->select('id as select_value', $label.' as select_label');
        $query->where($foreignKeyName, $foreignKeyValue);
        $query->orderby($label, 'asc');

        return response()->json($query->get());
    }

    public function getDataModalDatatable()
    {
        $data = base64_decode(json_decode(request('data'), true));

        $columns = explode(',', $data['columns']);

        $result = DB::table($data['table']);
        $this->applyQ($result, $columns);

        if ($data['sql_where']) {
            $result->whereraw($data['sql_where']);
        }

        $this->applyOrderBy($data, $result);
        $limit = ($data['limit']) ?: 6;

        return view('crudbooster::form.type_components.datamodal.browser', ['result' => $result->paginate($limit), 'data' => $data]);
    }

    public function getDataQuery()
    {
        $key = request('query');
        if (! Cache::has($key)) {
            return response()->json(['items' => []]);
        }
        $query = Cache::get($key);

        $fk_name = request('fk_name');
        $fk_value = request('fk_value');

        $condition = ' where ';
        if (strpos(strtolower($query), 'where') !== false) {
            $condition = ' and ';
        }

        if (strpos(strtolower($query), 'order by')) {
            $query = str_replace('ORDER BY', 'order by', $query);
            $qraw = explode('order by', $query);
            $query = $qraw[0].$condition.$fk_name." = '$fk_value' $qraw[1]";
        } else {
            $query .= $condition.$fk_name." = '$fk_value'";
        }

        $query = DB::select(DB::raw($query));

        return response()->json(['items' => $query]);
    }

    public function postFindData()
    {
        $items = app(Search::class)->searchData(request('data'), request('q'), request('id'));

        return response()->json(['items' => $items]);
    }

    /**
     * @param $result
     * @param $columns
     */
    private function applyQ($result, $columns)
    {
        if (request('q')) {
            return;
        }
        $result->where(function ($where) use ($columns) {
            foreach ($columns as $c => $col) {
                if ($c == 0) {
                    $where->where($col, 'like', '%'.request('q').'%');
                } else {
                    $where->orWhere($col, 'like', '%'.request('q').'%');
                }
            }
        });
    }

    /**
     * @param $data
     * @param $result
     */
    private function applyOrderBy($data, $result)
    {
        if ($data['sql_orderby']) {
            $result->orderByRaw($data['sql_orderby']);
        } else {
            $result->orderBy($data['column_value'], 'desc');
        }
    }
}