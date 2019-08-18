@extends('crudbooster::themes.adminlte.layout.layout')
@section('content')

    <form enctype="multipart/form-data" method="post" action="{{ route('AdminProfileControllerPostUpdate') }}">
        {!! csrf_field() !!}
    <div class="row">
        <div class="col-sm-3">
            <div class="form-group">

                @if(auth()->user()->photo)
                    <p align="center">
                        <img src="{{ asset(auth()->user()->photo) }}" width="230px" class="img-thumbnail" alt="Photo">
                    </p>
                @endif

                <input type="file" class="form-control" accept="image/*" name="photo" id="photo">
                <div class="help-block">File support jpg and png. Max size 500 KB</div>
            </div>
        </div>
        <div class="col-sm-9">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h1 class="box-title">Profile Data</h1>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name="name" id="name" value="{{ auth()->user()->name }}" required placeholder="Enter the name here">
                    </div>

                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" class="form-control" name="email" id="email" value="{{ auth()->user()->email }}" required placeholder="Enter the email here">
                    </div>



                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" placeholder="Please leave empty if not change" name="password" id="password">
                    </div>

                </div>
                <div class="box-footer">
                    <div align="center">
                        <button class="btn btn-success" type="submit"><i class="fa fa-save"></i> {{ cbLang("update") }} {{ cbLang("profile") }}</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </form>

@endsection