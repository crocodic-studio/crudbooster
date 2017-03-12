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

## Whats Next
- [Back To Index](./index.md)
