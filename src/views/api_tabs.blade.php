@extends('crudbooster::admin_template')

@section('content')

    <ul class="nav nav-tabs">
        <li class="active">
            <a href="{{ CRUDBooster::mainpath('documentation') }}">
                {!! CB::icon('file') !!} API Documentation
            </a>
        </li>
        <li><a href="{{ CRUDBooster::mainpath('screet-key') }}">{!! CB::icon('key') !!}API Screet Key</a></li>
        <li><a href="{{ CRUDBooster::mainpath('generator') }}">{!! CB::icon('cog') !!}API Generator</a></li>
    </ul>

    <div class='box'>
        <div class='box-header'><h3 class='box-title'>API Documentation</h3></div>
        <div class='box-body'>
            @include('crudbooster::api_documentation')
        </div>
    </div>


@endsection