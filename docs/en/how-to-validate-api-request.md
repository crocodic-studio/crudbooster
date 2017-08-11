### How To Validate API Request

In some case you need to make a pre-checking before executing API

CRUDBooster provide the method that you can use to do some validation before executing API

```php
public function hook_validate(&$postdata) {
    //Your any custom pre-checking
}
```
This special method come in API controller that you've genereated.

For example, you want to add user into cms_users through API and want to validate whether email already registered or not

```php
public function hook_validate(&$postdata) {
    $email = $postdata['email'];
    $checkmail = DB::table('cms_users')->where('email',$email)->first();;

    if($checkmail) {
      $this->hook_api_message = 'Email already registered!';
      $this->validate = true;
    }
}
```

## What's Next
- [How To Inject A Post Data In Create/Update/Delete Data Process](./how-to-inject-postdata.md)

## Table Of Contents
- [Back To Index](./index.md)