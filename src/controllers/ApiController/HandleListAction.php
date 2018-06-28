<?php

namespace Crocodicstudio\Crudbooster\controllers\ApiController;

class HandleListAction
{
    /**
     * @param $table
     * @param $data
     * @param $responsesFields
     * @param $ctrl
     * @return array
     */
    public static function handle($table, $data, $responsesFields, $ctrl)
    {
        $rows = self::sortRows($table, $data);
        if ($rows) {
            return self::handleRows($responsesFields, $rows);
        }
        $result = ApiResponder::makeResult(0, 'No data found !');
        $result['data'] = [];
        ApiResponder::send($result, request()->all(), $ctrl);
    }

    /**
     * @param $responsesFields
     * @param $rows
     * @return array
     */
    private static function handleRows($responsesFields, $rows)
    {
        foreach ($rows as &$row) {
            HandleDetailsAction::handleFile($row, $responsesFields);
        }

        $result = ApiResponder::makeResult(1, 'success');
        $result['data'] = $rows;

        return $result;
    }


    /**
     * @param $table
     * @param $data
     * @return mixed
     */
    private static function sortRows($table, $data)
    {
        $orderBy = request('orderby', $table.'.id,desc');

        list($orderByCol, $orderByVal) = explode(',', $orderBy);

        $rows = $data->orderby($orderByCol, $orderByVal)->get();

        return $rows;
    }

}