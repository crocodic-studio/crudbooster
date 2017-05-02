# Callback

### Table : Products
| Field Name | Data Type |
| ---------- | ----------- |
| id | int (PK) |
| name | varchar(255) |
| price | double |

### Products
```php
$this->col[] = ["label"=>"Name","name"=>"name"];
$this->col[] = ["label"=>"Price","name"=>"price","callback"=>function($row) {
return number_format($row->price);
}];
```

## What's Next
- [How To Make A Subquery Column in Grid Data](./how-to-make-subquery.md)

## Table Of Contents
- [Back To Index](./index.md)