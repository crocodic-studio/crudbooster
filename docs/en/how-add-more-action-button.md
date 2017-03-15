## How To Add More Action Button In Grid Data

### Products Table - products
| Field Name | Description | 
| ---------- | ----------- |
| id | int(PK) |
| created_at | timestamp |
| name | varchar(255) |
| description | varchar(255) |
| status | varchar(25) default 'pending' |

Open your module controller. Find `$this->addaction` in `cbInit()` method.

```php
$this->addaction[] = ['label'=>'Set Active','url'=>CRUDBooster::mainpath('set-status/active/[id]'),'icon'=>'fa fa-check','color'=>'success','showIf'=>"[status] == 'pending'"];
$this->addaction[] = ['label'=>'Set Pending','url'=>CRUDBooster::mainpath('set-status/pending/[id]'),'icon'=>'fa fa-ban','color'=>'warning','showIf'=>"[status] == 'active'"];
```
It will add **Set Active** button if a row has status 'pending', and the opposite, it will add **Set Pending** if a row has status 'active'

In the `showIf` attribute, you can fill anything condition with any other operator. You can use `[field_name]` as an alias.

Then, lets create a method to update the `products` status. Create a method after `cbInit()` method.
```php
public function getSetStatus($status,$id) {
   DB::table('products')->where('id',$id)->update(['status'=>$status]);
   
   //This will redirect back and gives a message
   CRUDBooster::redirect($_SERVER['HTTP_REFERER'],"The status product has been updated !","info");
}
```

## What's Next
- [Back To Index](./index.md)
