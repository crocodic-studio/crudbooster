# Laravel CRUDBooster
[![AUR](https://img.shields.io/aur/license/yaourt.svg?maxAge=2592000?style=flat-square)]()

> Laravel + CRUD Generator Package, Make a Web Application Just In Minutes, With Less Code and Less Step !

## System Requirement
Currently Based on Laravel 5.0
- PHP >= 5.4, PHP < 7
- Mcrypt PHP Extension
- OpenSSL PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- MySQL Database Server

## Installation
Please following these instructions : 
```
1. Download from github
2. Create folder in your htdocs, and extract
3. Create a blank database at your PHPMYADMIN
4. Go to http://localhost/YourApp/install/index.php
5. Follow the wizard installation until finish.
6. After installation is finish, don't forget to rename folder install to other name 
7. Done
```

## Table Structure Rule (IMPORTANT)
- **id** should as primary key 
- **id_[other_table_name]** this is format for secondary key (field for relation)
- **photo_[name]** this is not mandatory but i recommend you to use this format if you want the field as image field, replace [name] with your own suffix.
- **password** use this field name if you want add password, so Crudbooster can auto hashing
- **date_[name]** use this format of field if you want add column about 'date', so crudbooster know that this field is 'date'
- Get used to write a column name **clearly**. Avoid short name. Ex Correct : full_name, Ex Incorrect : fname

## Getting Started
I think you have made a table of database for your new module before do these steps. Now we want to generate CRUD modul Automatically, follow these steps :
```
http://localhost/YourApp/admin
username (default) : admin@crudbooster.com
password (default) : 123456

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
After you've created new modul, then a new menu will be appeared at left (sidebar left). You can view by click it. Done.

## Custom Controller and Form
Unfortunately in many case that very complicated problem, you can still custom the Controller and Form as you want. **CRUDBooster**  designed to be smart tool and can adapt in many cases and conditions. Every modul will be create a new file controller in /App/Http/Controllers/ .

## 1). Index Table List Data :
```php
$this->col = array();
$this->col[] = array('label'=>'Title','field'=>'title');
$this->col[] = array('label'=>'Short Description','field'=>'short_description');
$this->col[] = array('label'=>'Total Hit','field'=>'total_hit');
$this->col[] = array('label'=>'Category','field'=>'id_category','join'=>'category,name');
```

### Legends : 
| Name                  | Mandatory | Description                                                             |
| --------------------- | --------- | ----------------------------------------------------------------------- |
| label                 | Required  | label name                                                              |
| field                 | Required  | field tabel                                                             |
| join                  | Optional  | Relation tabel name. Ex : 'join'=>'table_name,name_field'             |
| image                 | Optional  | true or false                                                           |
| download              | Optional  | true or false                                                           |
| callback_html         | Optional  | Write html code here. Use object $row for current data. Use Single Quote |
| callback_php          | Optional  | Write php code here. Use object $row for current data. Use single quote |

## 2). Configure Form :
```php
$this->form = array();
$this->form[] = array('label'=>'Title','name'=>'title');
$this->form[] = array('label'=>'Short Description','name'=>'short_description','type'=>'textarea');
$this->form[] = array('label'=>'Total Hit','readonly'=>true);
$this->form[] = array('label'=>'Category','name'='id_category','type'=>'select','datatable'=>'category,name');
```

### Legends : 
| Name                  | Mandatory | Description                                                             |
| --------------------- | --------- | ----------------------------------------------------------------------- |
| label                 | Required | label name |
| name | Required | field tabel |
| type | Required | (text, textarea, radio, select, checkbox, wysiwyg, select2, datepicker, datetimepicker, hidden, password, upload, browse, qrcode) |
| dataenum | Optional | support only for type 'select,radio,checkbox', ex : array('BMW','MERCY','TOYOTA') |
| datatable | Optional | support only for type 'select,checkbox', this will load data from tabel, ex : "TABLE_NAME, COLUMN_NAME" |
| datatable_where | Optional | sql where query for datatable, ex : status != 'active' and status != 'available' |
| select2_controller | Optional | name other controller, ex : NameController | 
| sub_select | (optional) | name for child select. ex for case province and city. |
| help | (optional) | help text |
| placeholder | (optional) | placeholder text |
| image | (optional) | boolean |
| min | (optional) | value minimal |
| max | (optional) | value maximal |
| required | (optional) | boolean |
| mimes | (optional) | ex : jpg,png,gif |
| value | (optional) | Default value. |
| default_value | (optional) | force value. |
| browse_source | (optional) | Controller Name, ex : NameController |
| browse_columns | (optional) | Only if type is 'browse', ex : (new CompaniesController)->get_cols() |
| browse_where | (optional) | sql where query for browse , ex : status != 'active' and status != 'available' |
| type[qrcode][size ] | (required) | size of qr code |
| type[qrcode][color ] | (required) | color hex of qr code |
| type[qrcode][text ] | (required) | DOM id/name qr code |
| callback_php | (optional) | run php script |
| googlemaps | (optional) | false or true (Required field latitude and longitude) |
| googlemaps_address | (optional) | googlemaps required. Enter field location address. |
| html | (optional) | insert html code |
| jquery | (optional) | insert any jquery or javascript | 
| style | (optional) | insert any stylesheet inline for form-group class |
| join | (optional) | You can show join table field, **ex : members,name**, please make sure you've set **readonly** to be true otherwise make save data error |

## 3). Configure Form Tab (Children Module) :

### FORM TAB
```php
$this->form_tab = array();
$this->form_tab[] = array('label'=>'Photos','icon'=>'fa fa-bars','route'=>action("AdminPhotosController@getIndex"),'filter_field'=>'id_news');
```

#### Legends : 
| Name | Mandatory | Description |
| ---- | ---- | ---- |
| label | required | tab label name |
| icon | optional | set own awesome icon, ex : fa fa-bars |
| route | required | url for tab, ex : action('NameController@getIndex') |
| filter_field | required | field name that use for filter / relation field name. |

### FORM SUB
```php
$this->form_sub[] = array('label'=>'Photos','controller'=>'AdminPhotosController');
```

#### Legends : 
| Name | Mandatory | Description |
| ---- | ---- | ---- |
| label | required | label name table |
| controller | required | controller name that want to make as sub |

### FORM ADD 
```php
$this->form_add[] = "
<div id='form_other_element'>
	<div class='alert alert-warning'>You can manage modul News in this area.</div>
</div>
";
```
You can use form_add for add new element / html after all form elements 

## 4). Hook
| Name                             | Description                                    |
| -------------------------------- | ---------------------------------------------- |
| hook_before_index(&$result)      | Modify query mysql for index table             |
| hook_html_index(&$html_contents) | Modify row's html of table                     | 
| hook_before_add(&$postdata)      | Modify POST input data of user                 |
| hook_after_add($id)              | Create some action after Add data function is done |
| hook_before_edit(&$postdata,$id) | Modify POST input data of user                 | 
| hook_after_edit($id)             | Create some action after Edit/Update data function is done | 
| hook_before_delete($id)          | Create some action before delete function run  | 
| hook_after_delete($id)           | Create some action after delete function run   |

### Example : hook_before_index
```php
//In this case i will show you if there is a case that you want to show the status of data is active
public function hook_before_index(&$result) {
	$result->where('status','Active');
}
```
### Example : hook_html_index 
```php
public function hook_html_index(&$html_contents) {
	
	// If you want get data from each row, $html_contents['data']
	
	foreach($html_contents['html'] as &$row) {
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
### Example : hook_before_add
```php
public function hook_before_add(&$postdata) {
	//In this example, i want to add "date_order" so it can create time automatically 
	$postdata['date_order'] = date('Y-m-d H:i:s');
}
```
### Example : hook_after_add
```php
public function hook_after_add($id) {
	//in this example, i want insert data to other table after data inserted 
	
	$news = DB::table('news')->where('id',$id)->first();
	
	$data = array();
	$data['description'] = "I just insert new data for modul news with title '$title'";
	DB::table("somelogs")->insert($data);
}
```

## FAQ Problem Frequently 
1. Sometimes you've created a module correctly, but when you click the menu, you found that page is not found. So please make sure both file controller in /App/Http/Controllers and your Module is match. Also check your **route** in Edit Module at Backend.
2. The module wich you've made is not visible ? So please check at menu settings -> privileges, select correct privilege, then edit, please make sure the module wich you've made is checked. Also check at Edit Module, please make sure Active Status is Active.
