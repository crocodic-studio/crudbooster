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
- [Back To Index](./index.md)
