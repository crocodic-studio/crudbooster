# Radio Form Type
Showing radio button input type

### Code Sample
```php
$this->form[] = ['label'=>'Platform','name'=>'platform','type'=>'radio','dataenum'=>'Android;Ios;Website'];
```
**Important** . You need to set the dataenum attribute, then fill it with the options. The sparate is comma.

### Code Sample With Custom Value
```php
$this->form[] = ['label'=>'Platform','name'=>'platform','type'=>'radio','dataenum'=>'1|Android;2|Ios;3|Website'];
```
If you want to custom the value of radio button, then put `|` as a sparator between the value and label.

## What's Next
- [Back To Index](./index.md)
