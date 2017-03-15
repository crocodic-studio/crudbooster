# How To Make A Custom View Of Detail Method

A way to make a custom view of detail method is override it. This is a best way if CB can't handle the layout that you want.

```php
public function getDetail($id) {
  //Create an Auth
  if(!CRUDBooster::isRead() && $this->global_privilege==FALSE || $this->button_edit==FALSE) {    
    CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
  }
  
  $data = [];
  $data['page_title'] = 'Detail Data';
  $data['row'] = DB::table('products')->where('id',$id)->first();
  
  //Please use cbView method instead view method from laravel
  $this->cbView('custom_detail_view',$data);
}
```

Then, create your own `detail view`

```php
<!-- First, extends to the CRUDBooster Layout -->
@extends('crudbooster:admin_template')
@section('content')
  <!-- Your html goes here -->
  <div class='panel panel-default'>
    <div class='panel-heading'>Edit Form</div>
    <div class='panel-body'>      
        <div class='form-group'>
          <label>Name</label>
          <p>{{$row->name}}</p>
        </div>
         
        <!-- etc .... -->
        
      </form>
    </div>
  </div>
@endsection
```

## What's Next
- [Back To Index](./index.md)
