
namespace {!! ctrlNamespace() !!};

use {!! cbControllersNS() !!}\CBController;

class {{$controllerName}} extends CBController {

    public function cbInit() {
        {!! cbStartMarker('CONFIGURATION') !!}
        $this->table = "{{$table}}";
        $this->titleField = "{{DbInspector::colName($coloms)}}";
        $this->limit = 20;
        $this->showNumbering = false;
        $this->buttonTableAction = true;
        $this->orderby = "{{$pk}},desc";
        $this->buttonActionStyle = "button_icon";
        $this->buttonAdd = true;
        $this->deleteBtn = true;
        $this->buttonEdit = true;
        $this->buttonDetail = true;
        $this->buttonShow = true;
        $this->buttonFilter = true;
        $this->buttonExport = false;
        $this->buttonImport = false;
        $this->buttonBulkAction = true;
        {!! cbEndMarker('CONFIGURATION') !!}

        {!! cbStartMarker('COLUMNS') !!}
        $this->col = [];
        @foreach($cols as $col)
$this->col[] = ['label' => '{!! $col['label'] !!}', 'name' => {!! $col["name"] !!}];
        @endforeach
        {!! cbEndMarker('COLUMNS') !!}


        {!! cbStartMarker('FORM') !!}
        $this->form = [];
        @foreach($formArrayString as $formArray)
$this->form[] = {!! $formArray  !!};
        @endforeach
        {!! cbEndMarker('FORM') !!}

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
        */
        $this->sub_module = [];

        /*
        | ----------------------------------------------------------------------
        | Add More Action Button / Menu
        | ----------------------------------------------------------------------
        | @label    = Label of action
        | @url      = Target URL, you can use field alias. e.g : [id], [name], [title], etc
        | @icon     = Font awesome class icon. e.g : fa fa-bars
        | @color    = Default is primary. (primary, warning, succecss, info)
        | @showIf  = If condition when action show. Use field alias. e.g : [id] == 1
        */
        $this->addAction = [];

        /*
        | ----------------------------------------------------------------------
        | Add More Button Selected
        | ----------------------------------------------------------------------
        | @label = Label of action
        | @icon  = Icon from fontawesome
        | @name  = Name of button
        | Then about the action, you should code at actionButtonSelected method
        */
        $this->buttonSelected = [];

        /*
        | ----------------------------------------------------------------------
        | Add alert message to this module at overheader
        | ----------------------------------------------------------------------
        | @message = Text of message
        | @type    = warning,success,danger,info
        */
        $this->alert = [];

        /*
        | ----------------------------------------------------------------------
        | Add more button to header button
        | ----------------------------------------------------------------------
        | @label = Name of button
        | @url   = URL Target
        | @icon  = Icon from Awesome.
        */
        $this->indexButton = [];

        /*
        | ----------------------------------------------------------------------
        | Customize Table Row Color
        | ----------------------------------------------------------------------
        | @condition = If condition. You may use field alias. E.g : [id] == 1
        | @color = Default is none. You can use bootstrap success,info,warning,danger,primary.
        */
        $this->table_row_color = [];

        /*
        | ----------------------------------------------------------------------
        | You may use this bellow array to add statistic at dashboard
        | ----------------------------------------------------------------------
        | @label, @count, @icon, @color
        */
        $this->index_statistic = [];

        /*
        | ----------------------------------------------------------------------
        | Add javascript at body
        | ----------------------------------------------------------------------
        | javascript code in the variable
        | $this->scriptJs = "function() { ... }";
        */
        $this->scriptJs = NULL;

        /*
        | ----------------------------------------------------------------------
        | Include HTML Code before index table
        | ----------------------------------------------------------------------
        | html code to display it before index table
        | $this->preIndexHtml = "<p>test</p>";
        */
        $this->preIndexHtml = null;

        /*
        | ----------------------------------------------------------------------
        | Include HTML Code after index table
        | ----------------------------------------------------------------------
        | html code to display it after index table
        | $this->postIndexHtml = "<p>test</p>";
        */
        $this->postIndexHtml = null;

        /*
        | ----------------------------------------------------------------------
        | Include Javascript File
        | ----------------------------------------------------------------------
        | URL of your javascript each array
        | $this->load_js[] = asset("myfile.js");
        */
        $this->load_js = [];

        /*
        | ----------------------------------------------------------------------
        | Add css style at body
        | ----------------------------------------------------------------------
        | css code in the variable
        | $this->style_css = ".style{....}";
        */
        $this->style_css = NULL;

        /*
        | ----------------------------------------------------------------------
        | Include css File
        | ----------------------------------------------------------------------
        | URL of your css each array
        | $this->load_css[] = asset("myfile.css");
        */
        $this->load_css = [];

        }

    /*
    | ----------------------------------------------------------------------
    | Hook for button selected
    | ----------------------------------------------------------------------
    | @id_selected = the id selected
    | @button_name = the name of button
    */
    public function actionButtonSelected($id_selected,$button_name)
    {
        //Your code here
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for manipulate query of index result
    | ----------------------------------------------------------------------
    | @query = current sql query
    */
    public function hookQueryindex($query)
    {
        //Your code here
    @foreach ($joinList as $join)
        $query->join("{!! $join['table'] !!}", "{!! $join['field1'] !!}", "=", "{!! $join['field2'] !!}");
    @endforeach
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for manipulate row of index table html
    | ----------------------------------------------------------------------
    */
    public function hookRowIndex($index, $value)
    {
        //Your code here
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for manipulate data input before add data is execute
    | ----------------------------------------------------------------------
    | @arr
    */
    public function hookBeforeAdd($postData)
    {
        //You modify the $postData
        return $postData;
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for execute command after add public static function called
    | ----------------------------------------------------------------------
    | @id = last insert id
    */
    public function hookAfterAdd($id)
    {
        //Your code here
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for manipulate data input before update data is execute
    | ----------------------------------------------------------------------
    | @postdata = input post data
    | @id       = current id
    */
    public function hookBeforeEdit($postData, $id)
    {
        //Your code here
        return $postData;
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for execute command after edit public static function called
    | ----------------------------------------------------------------------
    */
    public function hookAfterEdit($id)
    {
        //Your code here
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for execute command before delete public static function called
    | ----------------------------------------------------------------------
    */
    public function hookBeforeDelete($id)
    {
        // Your code here
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for execute command after delete public static function called
    | ----------------------------------------------------------------------
    */
    public function hookAfterDelete($id)
    {
        //Your code here
    }

    //  By the way, you can still create your own method in here... :)

}