# Laravel CRUDBooster
Another Best Laravel CRUD Generator

#1). Configure Dashboard :

$this->col = array();__
$this->col[] = array('label'=>'NAMALABEL','field'=>'NAMAKOLOM_FIELD');____

Array $this->col is use for Dashboard Display__
There are some key :__

label (required) : label name__
field (required) : field tabel__
join (optional) : relational tabel name. Ex : 'join'=>'table_name,name_field'__
image (optional) : boolean true or false__
download (optional) : boolean true or false__
callback_html (optional) : Write any html code here. Use object $row for current data__
callback_php (optional) : Write any php code here. Use object $row for current data. Use single quote____
#2). Configure Form :__

$this->form = array();__
$this->form[] = array('label'=>'NAMALABEL','name'=>'NAMAFIELD');____

Array $this->form is use for Form Display.__
There are some key :____

label (required) : label name__
name (required) : field tabel__
type (required) : (text,textarea,radio,select,checkbox,wysiwyg,select2,datepicker,datetimepicker,hidden,password,upload,browse,qrcode)__
dataenum (optional) : support only for type 'select,radio,checkbox', ex : array('BMW','MERCY','TOYOTA') or array('0|BMW','1|MERCY','2|TOYOTA')__
datatable (optional) : support only for type 'select', this will load data from tabel, ex : "NAMATABEL, NAMAKOLOM_STRING"__
datatable_where (optional) : sql where query for datatable, ex : status != 'active' and status != 'available'__
select2_controller (optional) : name other controller, ex : NamaTabelController__
sub_select (optional) : name for child select. ex for case province and city.__
help (optional) : help text__
placeholder (optional) : placeholder text__
image (optional) : boolean__
min (optional) : value minimal__
max (optional) : value maximal__
required (optional) : boolean__
mimes (optional) : good for specify the file type upload, sparate by comma, ex : jpg,png,gif__
value (optional) : Any default value.__
default_value (optional) : force value.__
browse_source (optional) : Controller Name, ex : NameController__
browse_columns (optional) : Only if type is 'browse', ex : (new CompaniesController)->get_cols()__
browse_where (optional) : sql where query for browse , ex : status != 'active' and status != 'available'__
type[qrcode][size] (required) : size of qr code__
type[qrcode][color] (required) : color hex of qr code__
type[qrcode][text] (required) : DOM id/name qr code__
callback_php (optional) : run php script__
googlemaps (optional) : false or true (Required field latitude and longitude)__
googlemaps_address (optional) : googlemaps required. Enter field location address.__
html (optional) : insert html code__
jquery (optional) : insert any jquery or javascript__
style (optional) : insert any stylesheet inline for form-group class____

#3). Configure Form Tab (Children Module) :__

FORM TAB__
$this->form_tab = array();__
$this->form_tab[] = array('label'=>'NAMALABEL','icon'=>'fa fa-bars','route'=>'URL','filter_field'=>'RELATION_FIELD_NAME');__

Array $this->form_tab is use for children module tab. __
There are some key :__

label : tab label name__
icon : set own awesome icon, ex : fa fa-bars__
route : url for tab, ex : action('NamaTabelController@getIndex')__
filter_field : field name that use for filter / relation field name. __
If use this method / configure, you should add key `value` @ child form array. With example : $_GET['where'][FILTER_FIELD]__
dont forget replace FILTER_FIELD.__
FORM SUB__
$this->form_sub[] = array('label'=>'Label Name','controller'=>'Controller Name');__
Key :__
__
label : label name table__
controller : controller name that want to make as sub__
FORM ADD __

$this->form_add[] = "INSERT YOUR HTML HERE";__

#4). Hook__

This featured is for modify action after or before default do action. These bellow function name you can use__

hook_before_index(&$result)__
hook_html_index(&$html_contents)__
hook_before_add($add)__
hook_after_add($id)__
hook_before_edit($arr,$id)__
hook_after_edit($id)__
hook_before_delete($id)__
hook_after_delete($id)__
