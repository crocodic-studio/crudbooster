# How To Change The Color Of Row With Specific Condition

Some cases you need to change the color of row. For example is change the rows color that has status 'active' to be green color.

Open the module controller, find `$this->table_row_color` in `cbInit()` method.

```php
$this->table_row_color = array();
$this->table_row_color[] = ['condition'=>"[status] == 'active'","color"=>"success"];
```
`condition` attribute : you can use the field alias, for example : [id], [status], etc

`color` attribute : you can use : success, warning, info, danger, primary

## What's Next
- [Back To Index](./index.md)
