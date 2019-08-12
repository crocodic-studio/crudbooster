<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 5/10/2019
 * Time: 10:38 PM
 */

namespace crocodicstudio\crudbooster\types\select_table;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class SelectTableController extends BaseController
{
    public function postLookup() {
        $foreignKey = decrypt(request('foreign_key'));
        $foreignValue = request('foreign_value');
        $table = decrypt(request('table'));
        $sqlCondition = decrypt(request('sql_condition'));
        if($foreignKey && $foreignValue && $table) {

            $data = DB::table($table)
                ->where($foreignKey, $foreignValue);
            if($sqlCondition) {
                $data->whereRaw($sqlCondition);
            }

            $data = $data->get();

            return response()->json(['status'=>true, 'data'=>$data], 200, ["X-Frame-Options"=>"SAMEORIGIN"]);

        }else{
            return response()->json(['status'=>false]);
        }
    }
}