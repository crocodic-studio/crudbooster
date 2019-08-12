<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 1/26/2019
 * Time: 6:00 PM
 */

namespace crocodicstudio\crudbooster\types;

use crocodicstudio\crudbooster\controllers\scaffolding\traits\DefaultOption;
use crocodicstudio\crudbooster\controllers\scaffolding\traits\Join;
use crocodicstudio\crudbooster\models\ColumnModel;
use crocodicstudio\crudbooster\types\select_query\SelectQueryModel;
use Illuminate\Support\Facades\DB;

class SelectQuery
{
    use DefaultOption, Join;

    /**
     * @param callable|string $query DB Query Builder|SQL RAW
     * @return $this
     */
    public function optionsFromQuery($query) {
        $data = columnSingleton()->getColumn($this->index);
        /** @var $data SelectQueryModel */
        $data->setOptionsFromQuery($query);

        columnSingleton()->setColumn($this->index, $data);

        if(is_callable($query)) {
            $result = call_user_func($query);
        }else{
            $result = DB::select(DB::raw($query));
        }

        if($result) {
            $options = [];
            foreach($result as $r) {
                $options[ $r->key ] = $r->label;
            }
            $this->options($options);
        }

        return $this;
    }

    private function options($data_options) {
        $data = columnSingleton()->getColumn($this->index);
        /** @var $data SelectQueryModel */
        $data->setOptions($data_options);

        columnSingleton()->setColumn($this->index, $data);

        return $this;
    }
}