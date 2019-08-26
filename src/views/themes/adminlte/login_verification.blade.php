@extends('crudbooster::themes.adminlte.layout.layout_login')
@section('content')

    @if(!getSetting("login_logo"))
        <h1 style="text-align: center">{{ cb()->getAppName() }}</h1>
    @endif

    @include(themeFlashMessageAlert())

    <form id="form-login" autocomplete='off' action="{{ route('AdminAuthControllerPostSubmitLoginVerification') }}" method="post">
        {!! csrf_field() !!}
        <div class="form-group has-feedback">
            <label for="">Code Verification</label>
            <input autocomplete='off' type="text" class="form-control" name='code' required />
            <div class="help-block">Enter the code that you get from email</div>
        </div>
        <div style="margin-bottom:10px" class='row'>
            <div class='col-xs-12'>
                <button type="submit" class="btn btn-primary btn-block btn-flat">{{ cbLang("submit") }}</button>
            </div>
        </div>
    </form>

@endsection