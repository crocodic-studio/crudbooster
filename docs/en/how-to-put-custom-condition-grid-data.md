### How To Put A Custom Condition At Grid Data

In some case you need to make a condition of grid data query.

CRUDBooster provide the method that you can use to customize the query of grid data. 

```php
public function hook_query_index(&$query) {
  //Your any custom query
}
```
This special method come in each controller that you've genereated. If you don't find it, you may create it one.

For example, you have a table `products`: 

| Field Name | Data Type | 
| ---------- | --------- |
| id | int(PK) |
| craeted_at | timestamp |
| name | varchar(255) |
| description | text | 
| is_active | int(1) |

Now, you want to show the data where `is_active` is `1`

Your `hook_query_index` should be :

```php
public function hook_query_index(&$query) {
  $query->where('is_active',1);
}
```

## What's Next
- [Back To Index](./index.md)
