# Datamodal Form Type
Showing popup that contains a data table that you can choose

## Code Example
```php
$this->form[] = ['label'=>'Product Name','name'=>'products_id','type'=>'datamodal','datamodal_table'=>'products','datamodal_where'=>'','datamodal_columns'=>'name,description,price','datamodal_size'=>'large','required'=>true];	
```
## Legend
| Attribute Name | Example | Description
| -------------- | ----------- | --------- |
| datamodal_table | E.g : `products` | Table name |
| datamodal_where | E.g : `id != 1` | Sql query where |
| datamodal_columns | E.g : `name,description,price` | fields name to show. sparate with comma |
| datamodal_size | E.g : `large` or `default` | popup size |

## What's Next
- [Back To Index](./index.md)

