## How To Put Your Own Javascript

Open the module controller. Find `$this->script_js` in `cbInit()` method.

```php
$this->script_js = "
  $(function() {
     //Your custom javascript/jquery goes here
  });
";
```

## What's Next
- [How To Put Your Own CSS Stylesheet](./how-to-put-own-css.md)

## Table Of Contents
- [Back To Index](./index.md)