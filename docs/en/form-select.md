# Select Form Type
Showing a combo box (Spinner) input type

### Code Sample With Your Own Enum
```php
$this->form[] = ['label'=>'Platform','name'=>'platform','type'=>'select','dataenum'=>'Android;Ios;Website'];
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
$this->form[] = ['label'=>'Category','name'=>'categories_id','type'=>'select','datatable'=>'categories,name'];
```
in datatable attribute, first value is a table name, and the second value is a field wich you want to show as option label

## What's Next
- [Back To Index](./index.md)
