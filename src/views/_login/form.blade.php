<form autocomplete='off' action="{{ route('postLogin') }}" method="post">
    <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
    <div class="form-group has-feedback">
        <input autocomplete='off' type="text" class="form-control" name='email' required placeholder="Email"/>
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
    </div>
    <div class="form-group has-feedback">
        <input autocomplete='off' type="password" class="form-control" name='password' required
               placeholder="Password"/>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
    </div>
    <div style="margin-bottom:10px" class='row'>
        <div class='col-xs-12'>
            <button type="submit" class="btn btn-primary btn-block btn-flat">
                {!! CB::icon('lock') !!} {{cbTrans("button_sign_in")}}</button>
        </div>
    </div>

    <div class='row'>
        <div class='col-xs-12' align="center">
            <p style="padding:10px 0px 10px 0px">{{cbTrans("text_forgot_password")}}
                <a href='{{route("getForgot")}}'>{{cbTrans("click_here")}}</a>
            </p>
        </div>
    </div>
</form>
