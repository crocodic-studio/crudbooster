<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2/4/2019
 * Time: 8:30 AM
 */

namespace crocodicstudio\crudbooster\controllers\scaffolding\traits;

use crocodicstudio\crudbooster\controllers\scaffolding\singletons\ColumnSingleton;

trait Join
{
    public function join($target_table, $target_table_primary, $operator, $source_table_foreign, $type = 'left')
    {
        /** @var ColumnSingleton $column */
        $column = app('ColumnSingleton');
        $column->addJoin([
                'target_table'=>$target_table,
                'target_table_primary'=>$target_table_primary,
                'operator'=>$operator,
                'source_table_foreign'=>$source_table_foreign,
                'type'=>$type
            ]);
    }
}