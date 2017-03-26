# How To Make A Join Table in Grid Data

### Table : Products
| Field Name | Data Type |
| ---------- | ----------- |
| id | int (PK) |
| name | varchar(255) |
| description | varchar(255) |
| categories_id | int(11) |

### Table : Categories
| Field Name | Data Type | 
| ---- | ---- |
| id | int(PK) |
| name | varchar(50) |

### Products
```php
$this->col[] = ["label"=>"Name","name"=>"name"];
$this->col[] = ["label"=>"Description","name"=>"description"];
$this->col[] = ["label"=>"Category","name"=>"categories_id","join"=>"categories,name"];
```

## What's Next
- [Back To Index](./index.md)
