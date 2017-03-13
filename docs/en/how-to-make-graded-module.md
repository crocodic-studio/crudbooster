# How To Make A Graded Module (Parent Module -> Sub Module -> Sub Module)

### Albums Table
**albums**
| Field Name     | Data Type    |
| -------------- | ------------ |
| id             | int(PK)      |
| created_at     | timestamp    |
| name           | varchar(255) |
| description    | varchar(255) |

### Photos Table
**photos**
| Field Name | Data Type |
| ---------- | --------- |
| id | int(PK) |
| created_at | timestamp |
| name | varchar(255) |
| photo | varchar(255) |
| albums_id | int(11) |

### Code
```php
$this->sub_module[] = ['label'=>'Photos','path'=>'photos','parent_columns'=>'name,description','foreign_key'=>'albums_id','button_color'=>'success','button_icon'=>'fa fa-bars'];
```
![image](https://cloud.githubusercontent.com/assets/6733315/23846180/c91688da-07fe-11e7-93d6-20bafbfa36a7.png)
