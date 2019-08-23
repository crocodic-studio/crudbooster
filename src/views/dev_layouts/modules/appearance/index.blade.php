@extends("crudbooster::dev_layouts.layout")
@section("content")
    @push('head')
        <link rel="stylesheet" href="{{ cbAsset("adminlte/bower_components/bootstrap-toggle/bootstrap-toggle.min.css") }}">
    @endpush
    @push('bottom')
        <script src="{{ cbAsset("adminlte/bower_components/bootstrap-toggle/bootstrap-toggle.min.js") }}"></script>
    @endpush
    <div class="row">
        <div class="col-sm-8">

            <div class="box box-default">
                <form method="post" action="{{ route("DeveloperAppearanceControllerPostSave") }}">
                    {!! csrf_field() !!}
                <div class="box-body">
                    <div class="form-group">
                        <label for="">Application Name</label>
                        <input type="text" required name="APP_NAME" value="{{ getSetting("APP_NAME","CRUDBOOSTER")}}" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="">Login Background</label>
                        {!! cb()->htmlHelper()->makeImageUpload("Login Background","login_background", getSetting("login_background"), false) !!}
                        <p>
                            <input type="checkbox" name="login_background_cover" {{ getSetting("login_background_cover")=="on"?"checked":"" }} > Background size fit to screen
                        </p>
                    </div>

                    <div class="form-group">
                        <label for="">Login Logo</label>
                        {!! cb()->htmlHelper()->makeImageUpload("Login Logo","login_logo", getSetting("login_logo"), false) !!}
                        <span class="text-muted">
                                If you upload login logo, the login text title will be replaced with login logo
                            </span>
                    </div>


                    <div class="form-group">
                        <label for="">Change Login Page View</label>
                        <select name="login_page_view" class="form-control">
                            <option value="">:: Default (CB Login Page) ::</option>
                            <?php $views = rglob(resource_path("views/*.blade.php")); ?>
                            @foreach($views as $view)
                                @php $viewName = str_replace(resource_path("views")."/","",$view); @endphp
                                <option {{ $viewName==getSetting("login_page_view")?"selected":"" }} value="{{ $viewName }}">{{ $viewName }}</option>
                            @endforeach
                        </select>
                        <div class="help-block">Make sure that you are select the right login view file. The login page will be replaced with your selected view</div>
                    </div>
                </div>
                <div class="box-footer">
                    <input type="submit" class="btn btn-block btn-success" value="Save">
                </div>
                </form>
            </div><!--end register-->

        </div>
    </div>



@endsection