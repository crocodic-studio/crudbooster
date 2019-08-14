@extends('crudbooster::layouts.layout_login')
@section('content')

    @if(!getSetting("login_logo"))
        <h1 style="text-align: center">{{ cb()->getAppName() }}</h1>
    @endif

    <p class='login-box-msg text-muted' style="text-align: center">{{ cbLang("please_login_to_start_your_session")}}</p>

    @if ( Session::get('message') != '' )
        <div class='alert alert-warning'>
            {{ Session::get('message') }}
        </div>
    @endif

    <form autocomplete='off' action="{{ route('AdminAuthControllerPostLogin') }}" method="post">
        {!! csrf_field() !!}
        <div class="form-group has-feedback">
            <label for="">Email</label>
            <input autocomplete='off' type="text" class="form-control" name='email' required placeholder="Enter your email here"/>
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            <label for="">Password</label>
            <input autocomplete='off' type="password" class="form-control" name='password' required placeholder="Enter your password here"/>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div style="margin-bottom:10px" class='row'>
            <div class='col-xs-12'>
                <button type="submit" class="btn btn-primary btn-block btn-flat">{{ __("cb::cb.sign_in")}}</button>
            </div>
        </div>
    </form>
    <br>
    <p>
        Do you forget your password? <a href="#">Click here</a>
    </p>

@endsection