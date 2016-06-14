@extends('admin/admin_template')

@section('content')

    <div id='app'>
        <iframe width='100%' height="600px" style="border:0px" src='{{$url}}'></iframe>
    </div>

    <div class='clearfix'></div>

@endsection