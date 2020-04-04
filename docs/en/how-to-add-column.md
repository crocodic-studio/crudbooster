# How To Add Column At Grid Data
This documentation is for manual add column only instead by using Module Generator.

Open file controller that you want to add column at `app/Http/Controllers/AdminModuleController.php`
### Code Example
```php
$this->col[] = ['label'=>'Title','name'=>'title'];
```
### Legend
| Attribute     | Description     |
|---------------|-----------------|
|label  | label of column |
|name   | name for field name |

## What's Next
- [How To Join Table In Grid Data](./how-to-join-in-grid-data.md)

## Table Of Contents
- [Back To Index](./index.md)