# Money Form Type
Input text with auto money number format

### Code Sample
```php
$this->form[] = ['label'=>'Price','name'=>'price','type'=>'money'];
```
### Custom Money Format
```php
$this->form[] = ['label'=>'Price','name'=>'price','type'=>'money', 'priceformat_parameters' => ['prefix' => 'Rp. ', 'thousandsSeparator' => '.', 'centsSeparator' => ',', 'centsLimit' => 2 ]];
// Rp. 1.000,00
```
Add `priceformat_parameters` attribute value with [JQuery Price Format parameters](http://flaviosilveira.com/Jquery-Price-Format/)

## What's Next
- [Form Input Type: number](./form-number.md)

## Table Of Contents
- [Back To Index](./index.md)
