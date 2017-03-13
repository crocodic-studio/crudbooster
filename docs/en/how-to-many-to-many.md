### How To Implement A Many To Many Relationship To Form (E.g : Film -> R <- Actors)

### Films Table - films
| Field Name | Data Type |
| ---------- | --------- |
| id | int(PK) |
| created_at | timestamp |
| name | varchar(255) |
| description | text | 

### Actors Table - actors
| Field Name | Data Type |
| ---------- | --------- |
| id | int(PK) |
| name | varchar(255) |

### Films Actors Table - films_actors
| Field Name | Data Type |
| ---------- | --------- |
| id | int(PK) |
| films_id | int(11) |
| actors_id | int(11) |

We need multiple select box, so we use select2 with `datatable` and `relationship_table` attribute

### Code For Form of Films Controller
```php
$this->form[] - ['label'=>'Name','type'=>'text','name'=>'name'];
$this->form[] = ['label'=>'Description','type'=>'textarea','name'=>'description'];
$this->form[] = ['label'=>'Actors','type'=>'select2','datatable'=>'actors,name','relationship_table'=>'films_actors'];
```

## What's Next ?
- [Back To Index](./index.md)
