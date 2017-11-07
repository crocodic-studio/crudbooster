@extends('crudbooster::admin_template')

@section('content')

    @include('CbApiGen::_api_key.navtabs')

    <div class='box'>

        <div class='box-body'>


            <p>
                <a title='Generate API Key' class='btn btn-primary' href='javascript:void(0)'
                   onclick='generate_screet_key()'>
                    {!! CB::icon('key') !!} Generate Secret Key
                </a>
            </p>

            <table id='table-apikey' class='table table-striped table-bordered'>
                <thead>
                <tr>
                    <th width="3%">No</th>
                    <th>Screet Key</th>
                    <th width="10%">Hit</th>
                    <th width="10%">Status</th>
                    <th width="15%">-</th>
                </tr>
                </thead>
                <tbody>

                @foreach($apikeys as $no => $row)
                    @include('CbApiGen::_api_key.api_key')
                @endforeach


                @include('CbApiGen::_api_key.empty')

                </tbody>
            </table>

            @push('bottom')
                @include('CbApiGen::_api_key.script')
            @endpush

        </div>
    </div>

@endsection