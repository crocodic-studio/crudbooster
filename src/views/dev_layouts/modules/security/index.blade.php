@extends("crudbooster::dev_layouts.layout")
@section("content")
    @push('head')
        <link rel="stylesheet" href="{{ cbAsset("adminlte/bower_components/bootstrap-toggle/bootstrap-toggle.min.css") }}">
    @endpush
    @push('bottom')
        <script src="{{ cbAsset("adminlte/bower_components/bootstrap-toggle/bootstrap-toggle.min.js") }}"></script>
    @endpush
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-default">
                <form method="post" action="{{ route("DeveloperSecurityControllerPostSave") }}">
                    {!! csrf_field() !!}
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Debug Mode {!! isConfigCached()?"<sup class='text-muted'>Cached Mode</sup>":"" !!}</label>
                                <select name="APP_DEBUG" {{ isConfigCached()?"disabled":"" }} required class="form-control">
                                    <option {{ config("app.debug")===true?"selected":"" }} value="true">TRUE</option>
                                    <option {{ config("app.debug")===false?"selected":"" }} value="false">FALSE</option>
                                </select>
                                <div class="help-block">If Debug Mode set TRUE, error log will be shown directly on the page.
                                    <br><span class="text-danger">Warning: Showing error directly may give attacker information.</span></div>
                            </div>

                            <div class="form-group">
                                <label for="">App Path</label>
                                <input type="text" class="form-control" required name="ADMIN_PATH" value="{{ cb()->getAdminPath() }}">
                                <div class="help-block">App Path is main path of this app to entry</div>
                            </div>

                            <div class="form-group">
                                <label for="">Disable Login</label>
                                <input type="checkbox" name="DISABLE_LOGIN" {{ getSetting("DISABLE_LOGIN")?"checked":"" }} data-toggle="toggle" value="1">
                                <div class="help-block">To prevent any one login at your App, you can disable it</div>
                            </div>

                            <div class="form-group">
                                <label for="">Auto Suspend On Login Failed</label>
                                <input type="number" class="form-control" name="AUTO_SUSPEND_LOGIN" value="{{ getSetting("AUTO_SUSPEND_LOGIN")?:0 }}">
                                <div class="help-block">Fill this box with number value. Indicate how many times login failed before suspended for 30 minutes. Set to 0 to disable it</div>
                            </div>

                            <div class="form-group">
                                <label for="">Login Notification On New Device</label>
                                <input type="checkbox" name="LOGIN_NOTIFICATION" {{ getSetting("LOGIN_NOTIFICATION")?"checked":"" }} data-toggle="toggle" value="1">
                                <div class="help-block">Send a notification to user when sign in on new device</div>
                            </div>


                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Auto Redirect To App Login</label>
                                <input type="checkbox" name="AUTO_REDIRECT_TO_LOGIN" {{ getSetting("AUTO_REDIRECT_TO_LOGIN")?"checked":"" }} data-toggle="toggle" value="1">
                                <div class="help-block">Click On this toggle if you want to force auto redirect from homepage to app login page</div>
                            </div>

                            <div class="form-group">
                                <label for="">Disable Server OS Info <sup class="text-primary">(Apache only)</sup></label>
                                <input type="checkbox" name="htaccess_ServerSignature" {{ checkHtaccess("ServerSignature Off")===true?"checked disabled":"" }} data-toggle="toggle" value="true">
                                <div class="help-block">Disable server os information prevents attacker to know your OS</div>
                            </div>

                            <div class="form-group">
                                <label for="">Disable Index Listing <sup class="text-primary">(Apache only)</sup></label>
                                <input type="checkbox" name="htaccess_IndexIgnore" {{ checkHtaccess("IndexIgnore *")===true?"checked disabled":"" }} data-toggle="toggle" value="true">
                                <div class="help-block">Prevent attacker to know your files from listing index</div>
                            </div>

                            <div class="form-group">
                                <label for="">Prevent .env,.htaccess Access <sup class="text-primary">(Apache only)</sup></label>
                                <input type="checkbox" name="htaccess_dotAccess" {{ checkHtaccess('<FilesMatch "^\.">')===true?"checked disabled":"" }} data-toggle="toggle" value="true">
                                <div class="help-block">Attacker may access your .env, which is very dangerous. <strong>.env</strong> file contain important information such database credential. Click On to prevent it.</div>
                            </div>

                            <div class="form-group">
                                <label for="">Prevent files on /vendor directory access <sup class="text-primary">(Apache only)</sup></label>
                                <input type="checkbox" name="htaccess_preventVendor" {{ checkHtaccess('RewriteRule ^(.*)/vendor/.*')===true?"checked disabled":"" }} data-toggle="toggle" value="true">
                                <div class="help-block">If the /vendor library can access for public, it could be <strong>dangerous</strong>. Attacker can exploit any files inside your vendor directory</div>
                            </div>
                        </div>
                    </div>




                </div>
                <div class="box-footer">
                    <input type="submit" value="{{ cbLang("save") }}" class="btn btn-block btn-success">
                </div>
                </form>
            </div>
        </div>

        <div class="col-sm-6">

        </div>
    </div>



@endsection