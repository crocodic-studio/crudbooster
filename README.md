# Laravel CRUDBooster
> Another Best Laravel CRUD Generator

## 1). Configure Dashboard :
```
$this->col = array();
$this->col[] = array('label'=>'NAMALABEL','field'=>'NAMAKOLOM_FIELD');
```

Array $this->col is use for Dashboard Display
There are some key :

- label (required) : label name
- field (required) : field tabel
- join (optional) : relational tabel name. Ex : 'join'=>'table_name,name_field'
- image (optional) : boolean true or false
- download (optional) : boolean true or false
- callback_html (optional) : Write any html code here. Use object $row for current data
- callback_php (optional) : Write any php code here. Use object $row for current data. Use single quote

## 2). Configure Form :
```
$this->form = array();
$this->form[] = array('label'=>'NAMALABEL','name'=>'NAMAFIELD');
```

### Legends : 

- label (required) : label name
- name (required) : field tabel
- type (required) : (text,textarea,radio,select,checkbox,wysiwyg,select2,datepicker,datetimepicker,hidden,password,upload,browse,qrcode)
- dataenum (optional) : support only for type 'select,radio,checkbox', ex : array('BMW','MERCY','TOYOTA') or array('0|BMW','1|MERCY','2|TOYOTA')
- datatable (optional) : support only for type 'select', this will load data from tabel, ex : "NAMATABEL, NAMAKOLOM_STRING"
- datatable_where (optional) : sql where query for datatable, ex : status != 'active' and status != 'available'
- select2_controller (optional) : name other controller, ex : NamaTabelController
- sub_select (optional) : name for child select. ex for case province and city.
- help (optional) : help text
- placeholder (optional) : placeholder text
- image (optional) : boolean
- min (optional) : value minimal
- max (optional) : value maximal
- required (optional) : boolean
- mimes (optional) : good for specify the file type upload, sparate by comma, ex : jpg,png,gif
- value (optional) : Any default value.
- default_value (optional) : force value.
- browse_source (optional) : Controller Name, ex : NameController
- browse_columns (optional) : Only if type is 'browse', ex : (new CompaniesController)->get_cols()
- browse_where (optional) : sql where query for browse , ex : status != 'active' and status != 'available'
- type[qrcode][size] (required) : size of qr code
- type[qrcode][color] (required) : color hex of qr code
- type[qrcode][text] (required) : DOM id/name qr code
- callback_php (optional) : run php script
- googlemaps (optional) : false or true (Required field latitude and longitude)
- googlemaps_address (optional) : googlemaps required. Enter field location address.
- html (optional) : insert html code
- jquery (optional) : insert any jquery or javascript
- style (optional) : insert any stylesheet inline for form-group class

## 3). Configure Form Tab (Children Module) :

### FORM TAB
```
$this->form_tab = array();
$this->form_tab[] = array('label'=>'NAMALABEL','icon'=>'fa fa-bars','route'=>'URL','filter_field'=>'RELATION_FIELD_NAME');
```

#### Legends : 

- label : tab label name
- icon : set own awesome icon, ex : fa fa-bars
- route : url for tab, ex : action('NamaTabelController@getIndex')
- filter_field : field name that use for filter / relation field name. 
- If use this method / configure, you should add key `value` @ child form array. With example : $_GET['where'][FILTER_FIELD]
dont forget replace FILTER_FIELD.

### FORM SUB
- $this->form_sub[] = array('label'=>'Label Name','controller'=>'Controller Name');

#### Legends : 
- label : label name table
- controller : controller name that want to make as sub

### FORM ADD 
```
$this->form_add[] = "INSERT YOUR HTML HERE";
```

## 4). Hook
This functions is for modify action after or before default do action. 

- hook_before_index(&$result)
- hook_html_index(&$html_contents)
- hook_before_add($add)
- hook_after_add($id)
- hook_before_edit($arr,$id)
- hook_after_edit($id)
- hook_before_delete($id)
- hook_after_delete($id)
