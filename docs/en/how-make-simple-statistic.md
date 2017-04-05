# How To Make A Simple Statistic At Top Of Grid Data

This simple statistic will shown at top of grid data

```php
$this->index_statistic = array();
$this->index_statistic[] = ['label'=>'Total Data','count'=>DB::table('products')->count(),'icon'=>'fa fa-check','color'=>'success'];
```

## What's Next
- [How To Make A Child Form (Master - Detail)](./how-to-make-a-child-form.md)

## Table Of Contents
- [Back To Index](./index.md)