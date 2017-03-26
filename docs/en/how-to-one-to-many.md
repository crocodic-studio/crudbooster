### How To Implement An One To Many Relationship To Form (E.g : Product <- Category)

### Products Table - products
| Field Name | Description | 
| ---------- | ----------- |
| id | int(PK) |
| created_at | timestamp |
| name | varchar(255) |
| description | text |
| categories_id | int(11) |

### Categories Table - categories
| Field Name | Description |
| ---------- | ----------- |
| id | int(PK) |
| created_at | timestamp |
| name | varchar(255) |

### Form
```php
$this->form[] = ['label'=>'Name','name'=>'name','type'=>'text'];
$this->form[] = ['label'=>'Description','name'=>'description','type'=>'textarea'];
$this->form[] = ['label'=>'Category','name'=>'categories_id','type'=>'select','datatable'=>'categories,name'];
```
### Column
```php
$this->col[] = ['label'=>'Name','name'=>'name'];
$this->col[] = ['label'=>'Description','name'=>'description'];
$this->col[] = ['label'=>'Category','name'=>'categories_id','join'=>'categories,name'];
```

## What's Next
- [Back To Index](.index.md)
