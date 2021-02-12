# How To Make A Custom View Of Edit Method

A way to make a custom view of edit method is override it. This is a best way if CB can't handle the layout that you want.

```php
public function getEdit($id) {
  //Create an Auth
  if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE || $this->button_edit==FALSE) {    
    CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
  }
  
  $data = [];
  $data['page_title'] = 'Edit Data';
  $data['row'] = DB::table('products')->where('id',$id)->first();
  
  //Please use view method instead view method from laravel
  return $this->view('custom_edit_view',$data);
}
```

Then, create your own `add view`

```php
<!-- First, extends to the CRUDBooster Layout -->
@extends('crudbooster::admin_template')
@section('content')
  <!-- Your html goes here -->
  <div class='panel panel-default'>
    <div class='panel-heading'>Edit Form</div>
    <div class='panel-body'>
      <form method='post' action='{{CRUDBooster::mainpath('edit-save/'.$row->id)}}'>
        <div class='form-group'>
          <label>Name</label>
          <input type='text' name='name' required class='form-control' value='{{$row->name}}'/>
        </div>
         
        <!-- etc .... -->
        
      </form>
    </div>
    <div class='panel-footer'>
      <input type='submit' class='btn btn-primary' value='Save changes'/>
    </div>
  </div>
@endsection
```

## What's Next
- [How To Make A Custom View Of Detail Method](./how-to-custom-detail-view.md)

## Table Of Contents
- [Back To Index](./index.md)
