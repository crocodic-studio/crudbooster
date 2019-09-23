@extends("crudbooster::dev_layouts.layout")
@section("content")
    @push('head')
        <link rel="stylesheet" href="{{ cbAsset("js/bootstrap-toggle/bootstrap-toggle.min.css") }}">
    @endpush
    @push('bottom')
        <script src="{{ cbAsset("js/bootstrap-toggle/bootstrap-toggle.min.js") }}"></script>
    @endpush
    <form method="post" action="{{ route("DeveloperMiscellaneousControllerPostSave") }}">
        {!! csrf_field() !!}
        <div class="row">
        <div class="col-sm-6">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h1 class="box-title"><i class="fa fa-user"></i> Register Feature</h1>
                </div>

                    <div class="box-body">
                        <div class="form-group">
                            <label for="">Enable</label>
                            <input type="checkbox" {{ getSetting("enable_register")=="on"?"checked":"" }} name="enable_register" data-toggle="toggle">
                        </div>

                        <div class="form-group">
                            <label for="">Send Mail Verification</label>
                            <input type="checkbox" {{ getSetting("register_mail_verification")=="on"?"checked":"" }} name="register_mail_verification" data-toggle="toggle">
                            <p></p>
                            <input type="email" name="register_mail_verification_sender" value="{{ getSetting("register_mail_verification_sender","noreply@".$_SERVER['SERVER_NAME']) }}" class="form-control">
                            <div class="help-block">System will send a confirmation email to user email. Then they have to click the link verification inside the email. Please make sure that you have setting the
                                <a href="{{ cb()->getDeveloperUrl("mail") }}"><strong>Mail Configuration</strong></a></div>

                        </div>

                        <div class="form-group">
                            <label for="">Register As Role</label>
                            <select required name="register_as_role" class="form-control">
                                <option value="">** Select a Role</option>
                                <?php $roles = cb()->findAll("cb_roles"); ?>
                                @foreach($roles as $role)
                                    <option {{ getSetting("register_as_role")==$role->id?"selected":"" }} value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="box-footer">
                        <input type="submit" class="btn btn-success" value="Save Setting">
                    </div>

            </div><!--end register-->
        </div>
        <div class="col-sm-6">

            <div class="box box-default">
                <div class="box-header with-border">
                    <h1 class="box-title"><i class="fa fa-user"></i> Forget Password Feature</h1>
                </div>

                    <div class="box-body">
                        <div class="form-group">
                            <label for="">Enable</label>
                            <input type="checkbox" {{ getSetting("enable_forget")=="on"?"checked":"" }} name="enable_forget" data-toggle="toggle">
                        </div>

                        <div class="form-group">
                            <label for="">Email Sender</label>
                            <input type="email" name="forget_email_sender" value="{{ getSetting("forget_email_sender","noreply@".$_SERVER['SERVER_NAME']) }}" class="form-control">
                            <dib class="help-block">Fill the email forget sender</dib>
                        </div>

                    </div>
                    <div class="box-footer">
                        <input type="submit" class="btn btn-success" value="Save Setting">
                    </div>
            </div><!--end forget-->

        </div>
    </div>
    </form>
@endsection