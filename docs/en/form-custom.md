# Custom Form Type
You might create your own html input type by using `custom` type

### Code Sample
```php
$custom_element = view('custom_element')->render();
$this->form[] = ["label"=>"Label Name","name"=>"custom_field","type"=>"custom","html"=>$custom_element];
```

## What's Next
- [Back To Index](./index.md)
