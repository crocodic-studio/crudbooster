## How To Inject A Post Data In Create/Update/Delete Data Process

### Products Table - products
| Field Name | Description |
| ---------- | ------------|
| id | int(PK) |
| created_at | timestamp |
| name | varchar(255) |
| description | text |
| status | varchar(25) |

### Make a pre-custom-data at Create A Data
```php
public function hook_before_add(&$postdata) {  
  //For example you want to override the status field
  $postdata['status'] = 'Active';
}
```
This method will be called once submitting a form in Create A Data.

### Make A Pre-Custom-Data at Update A Data
```php
public function hook_before_edit(&$postdata,$id) {  
  //For example you want to override the status field
  $postdata['status'] = 'Active';
}
```
This method will be called once submitting a form in Update A Data.

### Make A Pre-Custom-Data at Delete A Data
```php
public function hook_before_delete($id) {  
  //You want to delete all data of child table
  DB::table('OTHER_CHILD_TABLE')->where('FOREIGN_KEY',$id)->delete();  
}
```
This method will be called once delete button is clicked.

## What's Next
- [How To Put Your Own Javascript](./how-to-put-your-own-javascript.md)

## Table Of Contents
- [Back To Index](./index.md)