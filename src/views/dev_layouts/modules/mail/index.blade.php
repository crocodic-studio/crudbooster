@extends("crudbooster::dev_layouts.layout")
@section("content")

    @if(isConfigCached())
        <div class="callout callout-warning">
            <strong>Notice</strong> This configuration is disabled because the laravel config is on "cache" mode. To modify this configuration, you have to disable it first (<code>php artisan config:clear</code>).
        </div>
    @endif

    <div class="row">
        <div class="col-sm-6">
            <div class="box box-default">
                <form method="post" action="{{ route("DeveloperMailControllerPostSave") }}">
                    {!! csrf_field() !!}
                <div class="box-body">
                    <div class="form-group">
                        <label for="">Mail Driver</label>
                        <input type="text" class="form-control" {{ isConfigCached()?"readonly":"" }} value="{{ config("mail.driver") }}" name="MAIL_DRIVER">
                        <div class="help-block">Supported: smtp, sendmail, mailgun, mandrill, ses,sparkpost, log, array</div>
                    </div>
                    <div class="form-group">
                        <label for="">Mail Host</label>
                        <input type="text" class="form-control" {{ isConfigCached()?"readonly":"" }} value="{{ config("mail.host") }}" name="MAIL_HOST">
                    </div>
                    <div class="form-group">
                        <label for="">Mail Port</label>
                        <input type="text" class="form-control" {{ isConfigCached()?"readonly":"" }} value="{{ config("mail.port") }}" name="MAIL_PORT">
                    </div>
                    <div class="form-group">
                        <label for="">Mail Username</label>
                        <input type="text" class="form-control" {{ isConfigCached()?"readonly":"" }} value="{{ config("mail.username") }}" name="MAIL_USERNAME">
                    </div>
                    <div class="form-group">
                        <label for="">Mail Password</label>
                        <input type="text" class="form-control" {{ isConfigCached()?"readonly":"" }} value="{{ config("mail.password") }}" name="MAIL_PASSWORD">
                    </div>
                    <div class="form-group">
                        <label for="">Mail Encryption</label>
                        <input type="text" class="form-control" {{ isConfigCached()?"readonly":"" }} value="{{ config("mail.encryption") }}" name="MAIL_ENCRYPTION">
                    </div>
                </div>
                <div class="box-footer">
                    <input type="submit" value="{{ cbLang("save") }}" {{ isConfigCached()?"disabled":"" }} class="btn btn-block btn-success">
                </div>
                </form>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="callout callout-info">
                <strong>Tips</strong><br><br>
                <p>
                    To send an email, we have write a simple helper that you can use : <br>

                    <div style="background: #fefefe; color: red;padding: 10px">
                    <?php
                        echo nl2br(trim('
                        use crocodicstudio\crudbooster\helpers\MailHelper;

                        $mail = new MailHelper();
                        $mail->sender($sender_email, $sender_name);
                        $mail->to($target_email);
                        $mail->subject($subject);
                        $mail->content($content);
                        $mail->send();
                        '));
                    ?>
                    </div>
                </p>
            </div>
        </div>
    </div>



@endsection