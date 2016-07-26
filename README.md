# Laravel CRUDBooster
> Faster Laravel CRUD Generator, Make a Web Application Just In Minutes, With Less Code and Less Step !

## Installation
Please following these instructions : 
```
1. Download from github
2. Create folder in your htdocs, and extract
3. Create a blank database at your PHPMYADMIN
4. Go to http://localhost/YourApp/install/index.php
5. Follow the wizard installation until finish.
6. Done
```

### Default Backend URL
```
http://localhost/YourApp/admin
username (default) : admin@crocodic.com
password (default) : 123456
```

## Getting Started
I think you have made a table database for your new module before do these bellow steps. 
```
1. Login to Admin
2. Go to Setting -> Modul Group
3. Click Pencil icon at Publik, to Edit
4. Click Plus Button at bottom, that has orange color.
5. Complete the Form : 
	- Name : Your new modul name
	- Table Name : Select your existing table that will be use
	- Route : declare url for this modul
	- Icon : set icon for this modul
	- Sorting : insert number of sorting (default 1)
6. Save
7. Done
```
After you create a new modul, then a new menu of your new modul will be appeare at left (sidebar left). You can view by click that new menu.

## 1). Configure Dashboard :
```php
$this->col = array();
$this->col[] = array('label'=>'LABEL NAME','field'=>'FIELD_NAME');
```

### Legends : 
- label (required) : label name
- field (required) : field tabel
- join (optional) : relational tabel name. Ex : 'join'=>'table_name,name_field'
- image (optional) : boolean true or false
- download (optional) : boolean true or false
- callback_html (optional) : Write any html code here. Use object $row for current data
- callback_php (optional) : Write any php code here. Use object $row for current data. Use single quote

## 2). Configure Form :
```php
$this->form = array();
$this->form[] = array('label'=>'LABEL NAME','name'=>'FIELD_NAME');
```

### Legends : 
- label (required) : label name
- name (required) : field tabel
- type (required) : (text, textarea, radio, select, checkbox, wysiwyg, select2, datepicker, datetimepicker, hidden, password, upload, browse, qrcode)
- dataenum (optional) : support only for type 'select,radio,checkbox', ex : array('BMW','MERCY','TOYOTA') or array('0|BMW','1|MERCY','2|TOYOTA')
- datatable (optional) : support only for type 'select', this will load data from tabel, ex : "TABLE_NAME, COLUMN_NAME"
- datatable_where (optional) : sql where query for datatable, ex : status != 'active' and status != 'available'
- select2_controller (optional) : name other controller, ex : NameController
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
```php
$this->form_tab = array();
$this->form_tab[] = array('label'=>'LABEL NAME','icon'=>'fa fa-bars','route'=>'URL','filter_field'=>'RELATION_FIELD_NAME');
```

#### Legends : 
- label : tab label name
- icon : set own awesome icon, ex : fa fa-bars
- route : url for tab, ex : action('NameController@getIndex')
- filter_field : field name that use for filter / relation field name. 

### FORM SUB
- $this->form_sub[] = array('label'=>'Label Name','controller'=>'Controller Name');

#### Legends : 
- label : label name table
- controller : controller name that want to make as sub

### FORM ADD 
```php
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

### Example hook_html_index 
```php
public function hook_html_index(&$html_contents) {

	foreach($html_contents as &$row) {
		// In this example, we want to coloring of status if Active then Green, Else then Red
		// First you should know where the status columns row locations (index of array) 
		$status = $row[5];
		if($status == 'Active') {
			$row[5] = "<span class='label label-success'>Active</span>";
		}else{
			$row[5] = "<span class='label label-danger'>Pending</span>";
		}
	}
}
```