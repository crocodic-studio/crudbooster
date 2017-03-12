# How To Make A Child Form (Master - Detail)
## Table Products : products 
| Field Name | Data Type |
| ---------- | --------- |
| id | int(PK) |
| crated_at | timestamp | 
| name | varchar(255) |
| price | double |

## Table Customers : customers
| Field Name | Data Type |
| ---------- | --------- |
| id | int(PK) |
| crated_at | timestamp | 
| name | varchar(255) |

## Table Master : orders
| Field Name | Data Type |
| ---------- | --------- |
| id | int(PK) |
| crated_at | timestamp | 
| customers_id | int(11) |
| order_number | varchar(25) |

## Table Detail : ordes_detail
| Field Name | Data Type |
| ---------- | --------- |
| id | int(PK) |
| created_at | timestamp |
| orders_id | int(11) |
| products_id | int(11) |
| price | double |
| discount | double |
| qty | int(10) |
| subtotal | double |

## Code Example			
```php
$columns[] = ['label'=>'Product','name'=>'products_id','type'=>'datamodal','datamodal_table'=>'products','datamodal_columns'=>'name,price','datamodal_select_to'=>'price:price','datamodal_where'=>'','datamodal_size'=>'large'];
$columns[] = ['label'=>'Price','name'=>'price','type'=>'number','required'=>true];
$columns[] = ['label'=>'QTY','name'=>'qty','type'=>'number','required'=>true];
$columns[] = ['label'=>'Discount','name'=>'discount','type'=>'number','required'=>true];
$columns[] = ['label'=>'Sub Total','name'=>'subtotal','type'=>'number','formula'=>"[qty] * [price] - [discount]","readonly"=>true,'required'=>true];
$this->form[] = ['label'=>'Orders Detail','name'=>'orders_detail','type'=>'child','columns'=>$columns,'table'=>'orders_detail','foreign_key'=>'orders_id'];
```

## Child Form Type Available
| Type Name | Additional Attribute |
| --------- | ----------- |
| text | min,max |
| number | min,max |
| textarea | max |
| select | datatable |
| radio | dataenum - e.g : ['a','b','c [, ...]] | 
| upload | upload_type - (image,file) |
| datamodal | datamodal_table,datamodal_columns,datamodal_select_to,datamodal_where,datamodal_size |

## What's Next
- [Back To Index](./index.md)
- How To Make A Datamodal Input Type
