# How To Make A Graded Module (Parent Module -> Sub Module -> Sub Module)

### Albums Table
| Field Name     | Data Type    |
| -------------- | ------------ |
| id             | int(PK)      |
| created_at     | timestamp    |
| name           | varchar(255) |
| description    | varchar(255) |

### Photos Table
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

### Legend
| Attribute Name | Description | 
| -------------- | ----------- |
| label | Specify the label of sub module |
| path | Specify the module path (slug) |
| parent_columns | Specify the parent fields in this case Album Fields that you want to show. Sparate it with comma |
| foreign_key | Specify the Foreign Key Field of photos table |
| button_color | Specify the color of button (primary,warning,success,info) |
| button_icon | Specify the icon. You can find out at Font Awesome |

## What's Next
- [Back To Index](./index.md)
