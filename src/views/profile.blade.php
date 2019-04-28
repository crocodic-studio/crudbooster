@extends('crudbooster::layouts.layout')
@section('content')

    <div class="box box-default">
        <div class="box-header">
            <h1 class="box-title">Profile Data</h1>
        </div>
        <form enctype="multipart/form-data" action="{{ action('AdminProfileController@postUpdate') }}">
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
                <label for="photo">Photo</label>
                <input type="file" class="form-control" name="photo" id="photo">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" id="password">
            </div>

            <div class="form-group">
                <label for="password_confirmation">Re-type Password</label>
                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation">
            </div>
        </div>
        <div class="box-footer">
            <input type="submit" class="btn btn-primary" value="Update Profile">
        </div>
        </form>
    </div>

@endsection