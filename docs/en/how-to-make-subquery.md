# How To Make A Subquery Column at Grid Data

### Table : Products
| Field Name | Data Type |
| ---------- | ----------- |
| id | int (PK) |
| name | varchar(255) |
| description | varchar(255) |

### Table : Favorite
| Field Name | Data Type | 
| ---- | ---- |
| id | int(PK) |
| members_id | int(11) |
| products_id | int(11) |

### Products
```php
$this->col[] = ["label"=>"Name","name"=>"name"];
$this->col[] = ["label"=>"Description","name"=>"description"];
$this->col[] = ["label"=>"Total Favorite","name"=>"(select count(favorite.id) from favorite where favorite.products_id = products.id) as total_favorite"];
```

## What's Next
- [How To Add More Action Button In Grid Data](./how-add-more-action-button.md)

## Table Of Contents
- [Back To Index](./index.md)