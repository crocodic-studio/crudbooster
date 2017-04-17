# Upload Form Input Type
Showing an upload input type

### Code Sample
```php
$this->form[] = ['label'=>'Document','name'=>'document','type'=>'upload','upload_encrypt'=>false];
```

### Upload With Image Validation
```php
$this->form[] = ['label'=>'Document','name'=>'document','type'=>'upload','validation'=>'image'];
```

### Upload With Image Validation and Size Validation
```php
//Validate the image and limit the size to 1000 KB
$this->form[] = ['label'=>'Document','name'=>'document','type'=>'upload','validation'=>'image|max:1000'];
```

## What's Next
- [Form Input Type: wysiwyg](./form-wysiwyg.md)

## Table Of Contents
- [Back To Index](./index.md)