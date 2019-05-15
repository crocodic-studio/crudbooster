@extends("crudbooster::dev_layouts.layout")
@section("content")


    <p>
        <a href="{{ route('DeveloperModulesControllerGetIndex') }}"><i class="fa fa-arrow-left"></i> Back To List</a>
    </p>

    <div class="box box-default">
        <div class="box-header">
            <h1 class="box-title">Add Module</h1>
        </div>
        <form method="post" action="{{ route('DeveloperModulesControllerPostAddSave') }}">
            {!! csrf_field() !!}
        <div class="box-body">
            <div class="form-group">
                <label for="">Name</label>
                <input required type="text" placeholder="E.g : Book Manager" name="name" class="form-control">
            </div>
            <div class="form-group">
                <label for="">Icon</label>
                <div class="row">
                    <div class="col-sm-5">
                        <div class="input-group">
                            <input type="text" name="icon" class="form-control" readonly >
                            <span class="input-group-btn">
                                <button class="btn btn-default" onclick="showIcon(this)" type="button">Choose Icon</button>
                            </span>
                        </div><!-- /input-group -->
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="">From Table</label>
                <select required name="table" class="form-control">
                    @foreach($tables as $table)
                        <option value="{{ $table }}">{{ $table }}</option>
                        @endforeach
                </select>
            </div>
        </div>
        <div class="box-footer">
            <input type="submit" class="btn btn-success" value="Generate">
        </div>
        </form>
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
                                <div class="panel-heading" role="tab" id="heading{{str_slug($f['label'])}}">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#{{ str_slug($f['label']) }}" aria-expanded="true">
                                            {{$f['label']}}
                                        </a>
                                    </h4>
                                </div>
                                <div id="{{ str_slug($f['label']) }}" class="panel-collapse collapse" role="tabpanel" >
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