### How To Make A Graded Select Boxes In A Form (Parent Select -> Child Select -> Child Select)

### Provinces Table - `provinces`
| Field Name | Data Type |
| ---------- | --------- |
| id | int(PK) |
| created_at | timestamp |
| name | varchar(255) |

### Cities Table - `cities`
| Field Name | Data Type |
| ---------- | --------- |
| id | int(PK) |
| created_at | timestamp |
| name | varchar(255) |
| provinces_id | int(11) |

### Code
```php
$this->form[] = ['label'=>'Province','type'=>'select','name'=>'provinces_id','datatable'=>'provinces,name'];
$this->form[] = ['label'=>'City','type'=>'select','name'=>'cities_id','datatable'=>'cities,name','parent_select'=>'provinces_id'];
```
So you need to add `parent_select` attribute and fill it with name of previous select **name**.

## What's Next
- [Back To Index](./index.md)
