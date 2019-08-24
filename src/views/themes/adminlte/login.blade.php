@extends('crudbooster::themes.adminlte.layout.layout_login')
@section('content')

    @if(!getSetting("login_logo"))
        <h1 style="text-align: center">{{ cb()->getAppName() }}</h1>
    @endif

    <p class='login-box-msg text-muted' style="text-align: center">{{ cbLang("please_login_to_start_your_session")}}</p>

    @include(themeFlashMessageAlert())

    <form id="form-login" autocomplete='off' action="{{ route('AdminAuthControllerPostLogin') }}" method="post">
        {!! csrf_field() !!}
        <div class="form-group has-feedback">
            <label for="">Email</label>
            <input autocomplete='off' type="email" class="form-control" name='email' required placeholder="Enter your email here"/>
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

                <br>
                <div class="row">
                    @if(getSetting("enable_register"))
                        <div class="col-sm-6">
                            <div align="left"><a style="text-decoration: underline" href="javascript:;" onclick="showRegister()">Don't have an account?</a></div>
                        </div>
                    @endif

                    @if(getSetting("enable_forget"))
                    <div class="col-sm-6">
                        <div align="right">
                            <a href="javascript:;" onclick="showForget()" style="text-decoration: underline">Lost your password?</a>
                        </div>
                    </div>
                    @endif
                </div>

            </div>
        </div>
    </form>

    @if(getSetting("enable_forget"))
    <form id="form-forget" style="display: none;" autocomplete='off' action="{{ route('AdminAuthControllerPostForget') }}" method="post">
        {!! csrf_field() !!}
        <div class="form-group has-feedback">
            <label for="">Email</label>
            <input autocomplete='off' type="email" class="form-control" name='email' required placeholder="Enter your registered email here"/>
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
            <div class="help-block">Please make sure that your email is registered on our system</div>
        </div>
        <div style="margin-bottom:10px" class='row'>
            <div class='col-xs-12'>
                <button type="submit" class="btn btn-primary btn-block btn-flat">{{ cbLang("submit") }}</button>
                <p></p>
                <p>
                    <a href="javascript:;" onclick="showLogin()" style="text-decoration: underline">&laquo; Back to Login</a>
                </p>
            </div>
        </div>
    </form>
    @endif

    @if(getSetting("enable_register"))
        <form id="form-register" style="display: none;" autocomplete='off' action="{{ route('AdminAuthControllerPostRegister') }}" method="post">
            {!! csrf_field() !!}
            <div class="form-group has-feedback">
                <label for="">Name</label>
                <input autocomplete='off' type="text" class="form-control" name='name' required />
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>

            <div class="form-group has-feedback">
                <label for="">Email</label>
                <input autocomplete='off' type="email" class="form-control" name='email' required />
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>

            <div class="form-group has-feedback">
                <label for="">Password</label>
                <input autocomplete='off' type="password" class="form-control" name='password' required placeholder="Enter your password here"/>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>

            <div class="form-group has-feedback">
                <label for="">Captcha</label>
                <p>What is the sum of {{ $no1 }} and {{ $no2 }}</p>
                <input autocomplete='off' type="text" placeholder="Enter the value here" class="form-control" name='captcha' required />

                <div class="help-block">Please complete this challenge</div>
            </div>
            <div style="margin-bottom:10px" class='row'>
                <div class='col-xs-12'>
                    <button type="submit" class="btn btn-success btn-block btn-flat">{{ cbLang("register") }}</button>
                    <p></p>
                    <p>
                        <a href="javascript:;" onclick="showLogin()" style="text-decoration: underline">&laquo; Back to Login</a>
                    </p>
                </div>
            </div>
        </form>
    @endif

    @push('bottom')
        <script>

            function showLogin() {
                $("form").hide()
                $("#form-login").fadeIn();
            }
            function showRegister() {
                $("form").hide()
                $("#form-register").fadeIn();
            }
            function showForget() {
                $("form").hide()
                $("#form-forget").fadeIn();
            }
        </script>
    @endpush

@endsection