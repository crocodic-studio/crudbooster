# How To Put Number Format at Grid Data Column

### Table : Products
| Field Name | Data Type |
| ---------- | ----------- |
| id | int (PK) |
| name | varchar(255) |
| price | double |

### Products
```php
$this->col[] = ["label"=>"Name","name"=>"name"];
$this->col[] = ["label"=>"Price","name"=>"price","callback_php"=>'number_format($row->price)';
```
or

```php
$this->col[] = ["label"=>"Name","name"=>"name"];
$this->col[] = ["label"=>"Price","name"=>"price","callback_php"=>'number_format([price])';
```
You can see we use **callback_php** . One think that you need to remember, don't use double quote instead use single quote.

## What's Next
- [How To Make A Subquery Column in Grid Data](./how-to-make-subquery.md)

## Table Of Contents
- [Back To Index](./index.md)