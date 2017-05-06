## How To Add More Bulk Action In Grid Data

Some cases you need to add a bulk button to gives a possibility set status to selected rows

### Products Table - products
| Field Name | Description |
| ---------- | ----------- |
| id | int(PK) |
| created_at | timestamp |
| name | varchar(255) |
| description | varchar(255) |
| status | varchar(25) default 'pending' |

First, you need add the button, so open the module controller, find `$this->button_selected` in `cbInit()` method.
```php
$this->button_selected = array();
$this->button_selected[] = ['label'=>'Set Active','icon'=>'fa fa-check','name'=>'set_active'];
```
It will create a new bulk button, but there is no an action. So we need to create it. Find `actionButtonSelected` method.
```php
public function actionButtonSelected($id_selected,$button_name) {
  //$id_selected is an array of id 
  //$button_name is a name that you have set at button_selected 
  
  if($button_name == 'set_active') {
    DB::table('products')->whereIn('id',$id_selected)->update(['status'=>'active']);
  }
}
```

## What's Next
- [How To Add More Button At Top Of Grid Data](./how-add-button-top-grid-data.md)

## Table Of Contents
- [Back To Index](./index.md)