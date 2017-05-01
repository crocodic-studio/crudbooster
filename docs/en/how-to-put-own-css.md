## How To Put Your Own CSS Stylesheet

Open the module controller. Find `$this->style_css` in `cbInit()` method.

```php
$this->style_css = "
  .some_class {
     color: yellow;
  }
";
```
or you can embed your own css url
```php
$this->load_css[] = asset("your_own_css_file.css");
```

## What's Next
- [How To Change The Application Name](./how-to-change-app-name.md)

## Table Of Contents
- [Back To Index](./index.md)