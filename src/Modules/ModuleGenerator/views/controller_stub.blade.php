
namespace App\Http\Controllers;

use Session;
use Request;
use DB;
use CRUDBooster;
use CB;

class Admin{{$controllerName}} extends \crocodicstudio\crudbooster\controllers\CBController {

public function cbInit() {
    # START CONFIGURATION DO NOT REMOVE THIS LINE
    $this->table 			   = "{{$table}}";
    $this->title_field         = "{{CRUDBooster::getNameTable($coloms)}}";
    $this->limit               = 20;
    $this->orderby             = "{{$pk}},desc";
    $this->show_numbering      = FALSE;
    $this->button_table_action = TRUE;
    $this->button_action_style = "button_icon";
    $this->button_add          = TRUE;
    $this->button_delete       = TRUE;
    $this->button_edit         = TRUE;
    $this->button_detail       = TRUE;
    $this->button_show         = TRUE;
    $this->button_filter       = TRUE;
    $this->button_export       = FALSE;
    $this->button_import       = FALSE;
    $this->button_bulk_action  = TRUE;
    # END CONFIGURATION DO NOT REMOVE THIS LINE

    # START COLUMNS DO NOT REMOVE THIS LINE
    $this->col = [];
    @foreach($cols as $col)
    $this->col[] = ['label' => '{!! $col['label'] !!}', 'name' => {!! $col["name"] !!}];
    @endforeach
    # END COLUMNS DO NOT REMOVE THIS LINE


    # START FORM DO NOT REMOVE THIS LINE
    $this->form = [];
    @foreach($formArrayString as $formArray)
    $this->form[] = {!! $formArray  !!};
    @endforeach

    # END FORM DO NOT REMOVE THIS LINE

    /*
    | ----------------------------------------------------------------------
    | Sub Module
    | ----------------------------------------------------------------------
    | @label          = Label of action
    | @path           = Path of sub module
    | @foreign_key 	  = foreign key of sub table/module
    | @button_color   = Bootstrap Class (primary,success,warning,danger)
    | @button_icon    = Font Awesome Class
    | @parentColumns = Sparate with comma, e.g : name,created_at
    |
    */
    $this->sub_module = [];


    /*
    | ----------------------------------------------------------------------
    | Add More Action Button / Menu
    | ----------------------------------------------------------------------
    | @label       = Label of action
    | @url         = Target URL, you can use field alias. e.g : [id], [name], [title], etc
    | @icon        = Font awesome class icon. e.g : fa fa-bars
    | @color 	   = Default is primary. (primary, warning, succecss, info)
    | @showIf 	   = If condition when action show. Use field alias. e.g : [id] == 1
    |
    */
    $this->addaction = [];


    /*
    | ----------------------------------------------------------------------
    | Add More Button Selected
    | ----------------------------------------------------------------------
    | @label       = Label of action
    | @icon 	   = Icon from fontawesome
    | @name 	   = Name of button
    | Then about the action, you should code at actionButtonSelected method
    |
    */
    $this->button_selected = [];


    /*
    | ----------------------------------------------------------------------
    | Add alert message to this module at overheader
    | ----------------------------------------------------------------------
    | @message = Text of message
    | @type    = warning,success,danger,info
    |
    */
    $this->alert = [];



    /*
    | ----------------------------------------------------------------------
    | Add more button to header button
    | ----------------------------------------------------------------------
    | @label = Name of button
    | @url   = URL Target
    | @icon  = Icon from Awesome.
    |
    */
    $this->index_button = [];



    /*
    | ----------------------------------------------------------------------
    | Customize Table Row Color
    | ----------------------------------------------------------------------
    | @condition = If condition. You may use field alias. E.g : [id] == 1
    | @color = Default is none. You can use bootstrap success,info,warning,danger,primary.
    |
    */
    $this->table_row_color = [];


    /*
    | ----------------------------------------------------------------------
    | You may use this bellow array to add statistic at dashboard
    | ----------------------------------------------------------------------
    | @label, @count, @icon, @color
    |
    */
    $this->index_statistic = [];



    /*
    | ----------------------------------------------------------------------
    | Add javascript at body
    | ----------------------------------------------------------------------
    | javascript code in the variable
    | $this->script_js = "function() { ... }";
    |
    */
    $this->script_js = NULL;


    /*
    | ----------------------------------------------------------------------
    | Include HTML Code before index table
    | ----------------------------------------------------------------------
    | html code to display it before index table
    | $this->pre_index_html = "<p>test</p>";
    |
    */
    $this->pre_index_html = null;



    /*
    | ----------------------------------------------------------------------
    | Include HTML Code after index table
    | ----------------------------------------------------------------------
    | html code to display it after index table
    | $this->post_index_html = "<p>test</p>";
    |
    */
    $this->post_index_html = null;



    /*
    | ----------------------------------------------------------------------
    | Include Javascript File
    | ----------------------------------------------------------------------
    | URL of your javascript each array
    | $this->load_js[] = asset("myfile.js");
    |
    */
    $this->load_js = [];



    /*
    | ----------------------------------------------------------------------
    | Add css style at body
    | ----------------------------------------------------------------------
    | css code in the variable
    | $this->style_css = ".style{....}";
    |
    */
    $this->style_css = NULL;



    /*
    | ----------------------------------------------------------------------
    | Include css File
    | ----------------------------------------------------------------------
    | URL of your css each array
    | $this->load_css[] = asset("myfile.css");
    |
    */
    $this->load_css = [];


    }

    /*
    | ----------------------------------------------------------------------
    | Hook for button selected
    | ----------------------------------------------------------------------
    | @id_selected = the id selected
    | @button_name = the name of button
    |
    */
    public function actionButtonSelected($id_selected,$button_name) {
    //Your code here

    }

    /*
    | ----------------------------------------------------------------------
    | Hook for manipulate query of index result
    | ----------------------------------------------------------------------
    | @query = current sql query
    |
    */
    public function hookQueryIndex(&$query) {
    //Your code here

    @foreach ($joinList as $join)
        $query->join("{!! $join['table'] !!}", "{!! $join['field1'] !!}", "=", "{!! $join['field2'] !!}");
    @endforeach
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for manipulate row of index table html
    | ----------------------------------------------------------------------
    |
    */
    public function hookRowIndex($column_index,&$column_value) {
    //Your code here
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for manipulate data input before add data is execute
    | ----------------------------------------------------------------------
    | @arr
    |
    */
    public function hookBeforeAdd(&$postdata) {
    //Your code here

    }

    /*
    | ----------------------------------------------------------------------
    | Hook for execute command after add public static function called
    | ----------------------------------------------------------------------
    | @id = last insert id
    |
    */
    public function hookAfterAdd($id) {
    //Your code here

    }

    /*
    | ----------------------------------------------------------------------
    | Hook for manipulate data input before update data is execute
    | ----------------------------------------------------------------------
    | @postdata = input post data
    | @id       = current id
    |
    */
    public function hookBeforeEdit(&$postdata,$id) {
    //Your code here

    }

    /*
    | ----------------------------------------------------------------------
    | Hook for execute command after edit public static function called
    | ----------------------------------------------------------------------
    | @id       = current id
    |
    */
    public function hookAfterEdit($id) {
    //Your code here

    }

    /*
    | ----------------------------------------------------------------------
    | Hook for execute command before delete public static function called
    | ----------------------------------------------------------------------
    | @id       = current id
    |
    */
    public function hookBeforeDelete($id) {
    //Your code here

    }

    /*
    | ----------------------------------------------------------------------
    | Hook for execute command after delete public static function called
    | ----------------------------------------------------------------------
    | @id       = current id
    |
    */
    public function hookAfterDelete($id) {
    //Your code here

    }

    //By the way, you can still create your own method in here... :)

}