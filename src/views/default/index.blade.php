@extends('crudbooster::admin_template')

@section('content')

    @if($index_additional_view && ($index_additional_view['position']=='top' || !$index_additional_view['position']))
        @include($index_additional_view['view'],$index_additional_view['data'])
    @endif
    
    <div class="panel panel-default">
      <div class="panel-heading">
          <strong><i class='{{CRUDBooster::getCurrentModule()->icon}}'></i> {!! $page_title or "Page Title" !!}</strong>      
      </div>
      <div class="panel-body">          
          @include("crudbooster::default.table")
      </div>
    </div> 


    @if($index_additional_view && $index_additional_view['position']=='bottom')
        @include($index_additional_view['view'],$index_additional_view['data'])
    @endif

@endsection
