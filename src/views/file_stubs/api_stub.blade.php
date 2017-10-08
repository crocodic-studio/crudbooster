
namespace App\Http\Controllers;

use Session;
use Request;
use DB;
use CRUDBooster;

class Api{{$controller_name}}Controller extends \crocodicstudio\crudbooster\controllers\ApiController {

    function __construct() {
        $this->table = "{{$table_name}}";
        $this->permalink = "{{$permalink}}";
        $this->method_type = "{{$method_type}}";
    }

    public function hook_before(&$postdata) {
        //This method will be execute before run the main process

    }

    public function hook_query(&$query) {
        //This method is to customize the sql query

    }

    public function hook_after($postdata,&$result) {
        //This method will be execute after run the main process

    }
