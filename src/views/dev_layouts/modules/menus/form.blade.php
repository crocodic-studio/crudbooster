@extends("crudbooster::dev_layouts.layout")
@section("content")

    <p>
        <a href="{{ route('DeveloperMenusControllerGetIndex') }}"><i class="fa fa-arrow-left"></i> Back To List</a>
    </p>

    <div class="row">
        <div class="col-sm-3"></div>
        <div class="col-sm-6">
            <div class="box box-default">
                <div class="box-header with-border">
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
                            <div class="input-group">
                                <input type="text" name="icon" class="form-control" value="{{ @$row->icon }}" readonly >
                                <span class="input-group-btn">
                                <button class="btn btn-default" onclick="showIcon(this)" type="button">Choose Icon</button>
                            </span>
                            </div><!-- /input-group -->
                            <div class="help-block">If the type of menu is Module, this icon will be overwrited by Modules Icon</div>
                        </div>

                        <div class="form-group">
                            <label for="">Type</label>
                            <select required name="type" id="type" onchange="if($(this).val()=='module') { $('#module-input-wrap').show(); } else { $('#module-input-wrap').hide(); }" class="form-control">
                                <option {{ (isset($row) && $row->type=="empty")?"selected":""  }} value="empty">Empty</option>
                                <option {{ (isset($row) && $row->type=="module")?"selected":"" }} value="module">Module</option>
                                <option {{ (isset($row) && $row->type=="url")?"selected":"" }} value="url">URL</option>
                                <option {{ (isset($row) && $row->type=="path")?"selected":"" }} value="path">Path (Prefix Admin URL)</option>
                            </select>
                            <div class="help-block"></div>
                        </div>
                        <div class="form-group" id="path-value-wrapper" style="display: none;">
                            <label for="">Path Value</label>
                            <input type="text" class="form-control" value="{{ (isset($row))?$row->path:null }}" name="path_value">
                        </div>
                        <div class="form-group" id="url-value-wrapper" style="display: none">
                            <label for="">URL Value</label>
                            <input type="url" class="form-control" value="{{ (isset($row))?$row->path:null }}" name="url_value">
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
                                        } else {
                                            $("#module-input-wrap select").prop('required', false).parent().hide();
                                            $("#path-value-wrapper input").prop("required", false).parent().hide();
                                            $("#url-value-wrapper input").prop("required", false).parent().hide();
                                        }
                                    })

                                    @if(isset($row) && $row->type=="module")
                                    $("#module-input-wrap").show();
                                    @endif
                                    @if(isset($row) && $row->type=="path")
                                    $("#path-value-wrapper").show();
                                    @endif
                                    @if(isset($row) && $row->type=="url")
                                    $("#url-value-wrapper").show();
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
        </div>
        <div class="col-sm-3"></div>
    </div>



    @push('bottom')
        <script>
            function showIcon(t) {
                $('#modal-fontawesome').modal('show');
            }

            function selectIcon(t) {
                let icon = $(t).data("icon");
                $("input[name=icon]").val(icon);
                $("#modal-fontawesome").modal("hide");
            }
        </script>
    @endpush
    @push('head')
        <style>
            .font-wrap {
                color: #000000;
                padding: 5px;
                text-align: center;
                background: #ffffff;
                border: 1px dotted #cccccc;
                display: block;
                font-size: 12px;
                height: 100px;
            }
            .font-wrap i {
                font-size: 30px;
            }
            .font-wrap:hover {
                background: #eeeeee;
                border-color: #0d6aad;
            }
            #accordion .panel-title a {
                display: block;
                color: #222222;
            }
        </style>
    @endpush

    <div class="modal fade" id="modal-fontawesome">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Choose Icon</h4>
                </div>
                <div class="modal-body">

                    <?php
                    $font = new \crocodicstudio\crudbooster\helpers\FontAwesome();
                    $fontData = [
                        ["data"=>$font->text(),"label"=>"Text"],
                        ["data"=>$font->web(),"label"=>"Web"],
                        ["data"=>$font->video(),"label"=>"Video"],
                        ["data"=>$font->transportation(),"label"=>"Transportation"],
                        ["data"=>$font->payment(),"label"=>"Payment"],
                        ["data"=>$font->medical(),"label"=>"Medical"],
                        ["data"=>$font->hand(),"label"=>"Hand"],
                        ["data"=>$font->fileType(),"label"=>"File Type"],
                        ["data"=>$font->directional(),"label"=>"Directional"],
                        ["data"=>$font->currency(),"label"=>"Currency"],
                        ["data"=>$font->chart(),"label"=>"Chart"],
                        ["data"=>$font->brands(),"label"=>"Brand"],
                        ["data"=>$font->gender(),"label"=>"Gender"],
                        ["data"=>$font->form(),"label"=>"Form"],
                        ["data"=>$font->spinner(),"label"=>"Spinner"]
                    ];
                    ?>
                    @foreach($fontData as $f)
                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="heading{{slug($f['label'])}}">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#{{ slug($f['label']) }}" aria-expanded="true">
                                            {{$f['label']}}
                                        </a>
                                    </h4>
                                </div>
                                <div id="{{ slug($f['label']) }}" class="panel-collapse collapse" role="tabpanel" >
                                    <div class="panel-body">
                                        <div class="row">
                                            @foreach($f['data'] as $icon)
                                                <div class="col-sm-2">
                                                    <a href="javascript:;" onclick="selectIcon(this)" data-icon="{{ $icon }}">
                                                        <div class="font-wrap">
                                                            <i class="{{ $icon }}"></i> <br/>{{ $icon }}
                                                        </div>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


@endsection