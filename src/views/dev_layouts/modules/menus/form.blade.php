@extends("crudbooster::dev_layouts.layout")
@section("content")


    <p>
        <a href="{{ route('DeveloperMenusControllerGetIndex') }}"><i class="fa fa-arrow-left"></i> Back To List</a>
    </p>

    <div class="box box-default">
        <div class="box-header">
            <h1 class="box-title">{{ $form_title }}</h1>
        </div>
        <form method="post" action="{{ $form_url }}">
            {!! csrf_field() !!}
        <div class="box-body">
            <div class="form-group">
                <label for="">Name</label>
                <input required type="text" placeholder="Type menu name here" value="{{ @$row->name }}" name="name" class="form-control">
            </div>

            <div class="form-group">
                <label for="">Icon</label>
                <input required type="text" placeholder="Fontawesome, e.g : fa fa-bars" value="{{ @$row->icon }}" name="icon" class="form-control">
            </div>

            <div class="form-group">
                <label for="">Type</label>
                <select required name="type" id="type" onchange="if($(this).val()=='module') { $('#module-input-wrap').show(); } else { $('#module-input-wrap').hide(); }" class="form-control">
                    <option {{ (isset($row) && $row->type=="module")?"selected":"" }} value="module">Module</option>
                    <option {{ (isset($row) && $row->type=="url")?"selected":"" }} value="url">URL</option>
                    <option {{ (isset($row) && $row->type=="path")?"selected":"" }} value="path">Path (Prefix Admin URL)</option>
                </select>
            </div>
            <div class="form-group" id="path-value-wrapper" style="display: none;">
                <label for="">Path Value</label>
                <input type="text" class="form-control" name="path_value">
            </div>
            <div class="form-group" id="url-value-wrapper" style="display: none">
                <label for="">URL Value</label>
                <input type="url" class="form-control" name="url_value">
            </div>
            @push('bottom')
            <script>
                $(function () {
                    $("#type").change(function () {
                        var v = $(this).val();
                        if(v == "module") {
                            $("#module-input-wrap select").prop('required', true).parent().show();
                            $("#path-value-wrapper input").prop("required", false).parent().hide();
                            $("#url-value-wrapper input").prop("required", false).parent().hide();
                        }else if(v == "url") {
                            $("#module-input-wrap select").prop('required', false).parent().hide();
                            $("#path-value-wrapper input").prop("required", false).parent().hide();
                            $("#url-value-wrapper input").prop("required", true).parent().show();
                        }else if(v == "path") {
                            $("#module-input-wrap select").prop('required', false).parent().hide();
                            $("#path-value-wrapper input").prop("required", true).parent().show();
                            $("#url-value-wrapper input").prop("required", false).parent().hide();
                        }
                    })

                    @if(isset($row) && $row->type=="module")
                        $("#module-input-wrap").show();
                    @endif
                    @if(!isset($row))
                        $("#module-input-wrap").show();
                    @endif
                })
            </script>
            @endpush

            <div class="form-group" id="module-input-wrap" style="display: none">
                <label for="">Module</label>
                <select {{ !isset($row)?"required":"" }} name="cb_modules_id" id="cb_modules_id" class="form-control">
                    <option value="">** Select a Module</option>
                    @foreach($modules as $module)
                        <option {{ (isset($row) && $row->cb_modules_id==$module->id)?"selected":"" }} value="{{ $module->id }}">{{ $module->name }}</option>
                    @endforeach
                </select>
            </div>

        </div>
        <div class="box-footer">
            <input type="submit" class="btn btn-success" value="Save Menu">
        </div>
        </form>
    </div>


@endsection