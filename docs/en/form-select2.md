# Select2 Form Type
Showing a select2 combo box (Spinner) input type. Select2 is the jQuery replacement for select boxes. Select2 gives you a customizable select box with support for searching, tagging, remote data sets, infinite scrolling, and many other highly used options

### Code Sample With Your Own Enum
```php
$this->form[] = ['label'=>'Platform','name'=>'platform','type'=>'select2','dataenum'=>'Android;Ios;Website'];
```
### Looking Up To A Table
#### categories Table
| Field Name | Data Type |
| ---------- | --------- |
| id | int(PK) |
| created_at | timestamp | 
| name | varchar(255) |

#### Code Sample
```php
$this->form[] = ['label'=>'Category','name'=>'categories_id','type'=>'select2','datatable'=>'categories,name'];
```
in datatable attribute, first value is a table name, and the second value is a field wich you want to show as option label

### Make A Condition to the Query
```php
$this->form[] = ['label'=>'Category','name'=>'categories_id','type'=>'select2','datatable'=>'categories,name','datatable_where'=>'id != 3'];
```
Add `datatable_where` attribute and fill it with `where` query like in sql.

### Customize the label of option
```php
$this->form[] = ['label'=>'Category','name'=>'categories_id','type'=>'select2','datatable'=>'categories,name','datatable_format'=>"id,' - ',name"];
```
Add `datatable_format` attribute and fill it with a format. Specify the field name wich you want to show. You may specify the field more than one. Sparate it with a comma. If there is a word in addition to the field name, you must put a single quote in the prefix and suffix to its word. (See the example).


## What's Next
- [Back To Index](./index.md)
