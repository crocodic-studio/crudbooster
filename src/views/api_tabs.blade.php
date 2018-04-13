@extends('crudbooster::admin_template')

@section('content')

    <!-- Custom Tabs -->
    <ul class="nav nav-tabs">
        <li class="active"><a href="{{ CRUDBooster::mainpath('documentation') }}"><i class='fa fa-file'></i> API Documentation</a></li>
        <li><a href="{{ CRUDBooster::mainpath('screet-key') }}"><i class='fa fa-key'></i> API Screet Key</a></li>
        <li><a href="{{ CRUDBooster::mainpath('generator') }}"><i class='fa fa-cog'></i> API Generator</a></li>
    </ul>

    <div class='box'>
        <div class='box-header'><h3 class='box-title'>API Documentation</h3></div>
        <div class='box-body'>
            @include('crudbooster::api_documentation')
        </div>
    </div>

    <!-- nav-tabs-custom -->

@endsection