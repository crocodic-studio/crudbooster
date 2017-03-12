# Checkbox Form Type

### Checkbox With Your Own Enum
```php
$this->form[] = ['label'=>'Platform','name'=>'platform','type'=>'checkbox','dataenum'=>'Android;IOS;Website'];
```

### Checkbox With Looking Up Table
```php
$this->form[] = ['label'=>'Platform','name'=>'platform','type'=>'checkbox','datatable'=>'platform,name'];
```

### Legend
| Attribute Name | Example |
| -------------- | ------- |
| dataenum | `Android;Ios;Website` . sparate with comma |
| datatable | `table_name,field_name` . field_name is field that want to show as label |

## What's Next
- [Back To Index](./index.md)
