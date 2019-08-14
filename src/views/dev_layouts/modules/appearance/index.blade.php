@extends("crudbooster::dev_layouts.layout")
@section("content")
    @push('head')
        <link rel="stylesheet" href="{{ cbAsset("adminlte/bower_components/bootstrap-toggle/bootstrap-toggle.min.css") }}">
    @endpush
    @push('bottom')
        <script src="{{ cbAsset("adminlte/bower_components/bootstrap-toggle/bootstrap-toggle.min.js") }}"></script>
    @endpush
    <div class="row">
        <div class="col-sm-6">

            <div class="box box-default">
                <div class="box-header with-border">
                    <h1 class="box-title"><i class="fa fa-image"></i> CMS Appearance</h1>
                </div>
                <form method="post" action="{{ route("DeveloperAppearanceControllerPostSave") }}">
                    {!! csrf_field() !!}
                    <div class="box-body">
                        <div class="form-group">
                            <label for="">Application Name</label>
                            <input type="text" required name="APP_NAME" value="{{ getSetting("APP_NAME")}}" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="">CMS Background Color</label>
                            <input type="color" name="cms_background_color" value="{{ getSetting("cms_background_color","#fefefe") }}" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="">CMS Body Font Size</label>
                            <input type="number" name="cms_font_size" value="{{ getSetting("cms_font_size", 14) }}" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="">CMS Body Font Color</label>
                            <input type="color" name="cms_body_color" value="{{ getSetting("cms_body_color","#000000") }}" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="">CMS Additional CSS</label>
                            <textarea name="cms_additional_css" rows="6" placeholder="body { color: #000000 }" class="form-control">{{ getSetting("cms_additional_css") }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="">CMS Theme Color</label>
                            <select name="cms_theme_color" class="form-control">
                                <option {{ getSetting("cms_theme_color","skin-blue")=="skin-black"?"selected":"" }} value="skin-black">Black</option>
                                <option {{ getSetting("cms_theme_color","skin-blue")=="skin-black-light"?"selected":"" }} value="skin-black-light">Black Light</option>
                                <option {{ getSetting("cms_theme_color","skin-blue")=="skin-blue"?"selected":"" }} value="skin-blue">Blue</option>
                                <option {{ getSetting("cms_theme_color","skin-blue")=="skin-blue-light"?"selected":"" }} value="skin-blue-light">Blue Light</option>
                                <option {{ getSetting("cms_theme_color","skin-blue")=="skin-green"?"selected":"" }} value="skin-green">Green</option>
                                <option {{ getSetting("cms_theme_color","skin-blue")=="skin-green-light"?"selected":"" }} value="skin-green-light">Green Light</option>
                                <option {{ getSetting("cms_theme_color","skin-blue")=="skin-purple"?"selected":"" }} value="skin-purple">Purple</option>
                                <option {{ getSetting("cms_theme_color","skin-blue")=="skin-purple-light"?"selected":"" }} value="skin-purple-light">Purple Light</option>
                                <option {{ getSetting("cms_theme_color","skin-blue")=="skin-red"?"selected":"" }} value="skin-red">Red</option>
                                <option {{ getSetting("cms_theme_color","skin-blue")=="skin-red-light"?"selected":"" }} value="skin-red-light">Red Light</option>
                                <option {{ getSetting("cms_theme_color","skin-blue")=="skin-yellow"?"selected":"" }} value="skin-yellow">Yellow</option>
                                <option {{ getSetting("cms_theme_color","skin-blue")=="skin-yellow-light"?"selected":"" }} value="skin-yellow-light">Yellow Light</option>
                            </select>
                        </div>
                    </div>

                    <div class="box-footer">
                        <input type="submit" value="{{ cbLang("save") }}" class="btn btn-block btn-success">
                    </div>
                </form>
            </div>
        </div>
        <div class="col-sm-6">

            <div class="box box-default">
                <div class="box-header with-border">
                    <h1 class="box-title"><i class="fa fa-key"></i> Login Appearance</h1>
                </div>
                <form method="post" action="{{ route("DeveloperAppearanceControllerPostSave") }}">
                    {!! csrf_field() !!}
                <div class="box-body">
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
                    <input type="submit" class="btn btn-success" value="Save Setting">
                </div>
                </form>
            </div><!--end register-->

        </div>
    </div>



@endsection