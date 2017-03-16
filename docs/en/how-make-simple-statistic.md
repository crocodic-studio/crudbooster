# How To Make A Simple Statistic At Top Of Grid Data

This simple statistic will shown at top of grid data

```php
$this->index_statistic = array();
$this->index_statistic[] = ['label'=>'Total Data','count'=>DB::table('products')->count(),'icon'=>'fa fa-check','color'=>'success'];
```

## What's Next
- [Back To Index](./index.md)
