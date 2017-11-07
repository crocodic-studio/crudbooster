@extends("crudbooster::admin_template")
@section("content")
    @push('head')
        {!! cbStyleSheet('select2/dist/css/select2.min.css') !!}
        <style>
            .select2-container--default .select2-selection--single {
                border-radius: 0px !important
            }

            .select2-container .select2-selection--single {
                height: 35px
            }
        </style>
    @endpush
    @push('bottom')
        {!! cbScript("select2/dist/js/select2.full.min.js") !!}
        <script>
            $(function () {
                $('.select2').select2();
            })
        </script>
    @endpush

    @include('CbModulesGen::partials.nav_tabs', ['step' => ['','active','',''], 'id' => $id ])
    @push('head')
        <style>
            .table-display tbody tr td {
                position: relative;
            }

            .sub {
                position: absolute;
                top: inherit;
                left: inherit;
                padding: 0 0 0 0;
                list-style-type: none;
                height: 180px;
                overflow: auto;
                z-index: 1;
            }

            .sub li {
                padding: 5px;
                background: #eae9e8;
                cursor: pointer;
                display: block;
                width: 180px;
            }

            .sub li:hover {
                background: #ECF0F5;
            }

            .btn-drag {
                cursor: move;
            }
        </style>
    @endpush

    @push('bottom')
        @include('CbModulesGen::step2.js')
    @endpush

    <form method="post" action="{{Route('AdminModulesControllerPostStep3')}}">

        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Table Display</h3>
            </div>
            <div class="box-body">

                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <input type="hidden" name="id" value="{{$id}}">

                <table class="table-display table table-striped">
                    <thead>
                    <tr>
                        <th>Column</th>
                        <th>Name</th>
                        <th width="90px">Width (px)</th>
                        <th width='80px'>Image</th>
                        <th width='80px'>Download</th>
                        <th>&nbsp;</th>
                        <th width="180px">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($cols)
                        @foreach($cols as $c)
                            <tr>
                                <td><input value='{{$c["label"]}}' type='text' name='column[]'
                                           onclick='showColumnSuggest(this)' onKeyUp='showColumnSuggestLike(this)'
                                           placeholder='Column Name' class='column form-control notfocus' value=''/>
                                </td>
                                <td><input value='{{$c["name"]}}' type='text' name='name[]'
                                           onclick='showNameSuggest(this)' onKeyUp='showNameSuggestLike(this)'
                                           placeholder='Field Name' class='name form-control notfocus' value=''/></td>
                                <td><input value='{{$c["width"]?:0}}' type='number' name='width[]'
                                           class='form-control'/></td>
                                <td>
                                    <select class='form-control is_image' name='is_image[]'>
                                        <option {{ (!$c['image'])?"selected":""}} value='0'>N</option>
                                        <option {{ ($c['image'])?"selected":""}} value='1'>Y</option>
                                    </select>
                                </td>
                                <td>
                                    <select class='form-control is_download' name='is_download[]'>
                                        <option {{ (!$c['download'])?"selected":""}} value='0'>N</option>
                                        <option {{ ($c['download'])?"selected":""}} value='1'>Y</option>
                                    </select>
                                </td>
                                <td>
                                    <a href="javascript:void(0)" class="btn btn-primary btn-callback"><i
                                                class="fa fa-pencil"></i> Callback</a>
                                    <input type="hidden" class="input-callback" name="callback[]"
                                           value="{{$c['callback']}}">
                                </td>
                                <td>
                                    <a href="javascript:void(0)" class="btn btn-info btn-plus"><i
                                                class='fa fa-plus'></i></a>
                                    <a href="javascript:void(0)" class="btn btn-danger btn-delete"><i
                                                class='fa fa-trash'></i></a>
                                    <a href="javascript:void(0)" class="btn btn-success btn-up"><i
                                                class='fa fa-arrow-up'></i></a>
                                    <a href="javascript:void(0)" class="btn btn-success btn-down"><i
                                                class='fa fa-arrow-down'></i></a>
                                </td>
                            </tr>
                        @endforeach
                    @endif

                    <tr id="tr-sample" style="display:none">
                        <td><input type='text' name='column[]' onclick='showColumnSuggest(this)'
                                   onKeyUp='showColumnSuggestLike(this)' placeholder='Column Name'
                                   class='column form-control notfocus' value=''/></td>
                        <td><input type='text' name='name[]' onclick='showNameSuggest(this)'
                                   onKeyUp='showNameSuggestLike(this)' placeholder='Field Name'
                                   class='name form-control notfocus' value=''/></td>
                        <td><input type='number' name='width[]' value='0' class='form-control'/></td>
                        <td>
                            <select class='form-control is_image' name='is_image[]'>
                                <option value='0'>N</option>
                                <option value='1'>Y</option>
                            </select>
                        </td>
                        <td>
                            <select class='form-control is_download' name='is_download[]'>
                                <option value='0'>N</option>
                                <option value='1'>Y</option>
                            </select>
                        </td>
                        <td>
                            <a href="javascript:void(0)" class="btn btn-primary btn-callback"><i
                                        class="fa fa-pencil"></i> Callback</a>
                            <input type="hidden" class="input-callback" name="callback[]">
                        </td>
                        <td>
                            <a href="javascript:void(0)" class="btn btn-info btn-plus"><i class='fa fa-plus'></i></a>
                            <a href="javascript:void(0)" class="btn btn-danger btn-delete"><i
                                        class='fa fa-trash'></i></a>
                            <a href="javascript:void(0)" class="btn btn-success btn-up"><i
                                        class='fa fa-arrow-up'></i></a>
                            <a href="javascript:void(0)" class="btn btn-success btn-down"><i
                                        class='fa fa-arrow-down'></i></a>
                        </td>
                    </tr>

                    </tbody>
                </table>

            </div>
            <div class="box-footer">
                <div align="right">
                    <button type="button" onclick="location.href='{{CRUDBooster::mainpath('step1').'/'.$id}}'"
                            class="btn btn-default">&laquo; Back
                    </button>

                    <button class="btn btn-success btn-sm" type="submit">
                        {!! CB::icon('save') !!}Save Module
                    </button>

                </div>
            </div>

        </div>

    @push('bottom')
        {!! cbScript("codemirror/lib/codemirror.js") !!}
        {!! cbScript("codemirror/addon/edit/matchbrackets.js") !!}
        {!! cbScript("codemirror/mode/htmlmixed/htmlmixed.js") !!}
        {!! cbScript("codemirror/mode/xml/xml.js") !!}
        {!! cbScript("codemirror/mode/javascript/javascript.js") !!}
        {!! cbScript("codemirror/mode/css/css.js") !!}
        {!! cbScript("codemirror/mode/clike/clike.js") !!}
        {!! cbScript("codemirror/mode/php/php.js") !!}
        {!! cbScript("codemirror/keymap/sublime.js") !!}
        @include('CbModulesGen::step2.script')

        @include('CbModulesGen::step2.modal')
        <!-- /.modal -->
        @endpush
        @push('head')
            <link rel="stylesheet" type="text/css"
                  href="{{cbAsset('codemirror/lib/codemirror.css')}}">
            <link rel="stylesheet" type="text/css"
                  href="{{cbAsset('codemirror/theme/blackboard.css')}}">
        @endpush

        <div class="box box-default">
            @include('CbModulesGen::step2.hookRowQuery')
        </div>

        <div class="box box-default">
            @include('CbModulesGen::step2.hookRowIndex')
        </div>

        <div class="box box-default">
            @include('CbModulesGen::step2.hookBeforeAdd')
        </div>

        <div class="box box-default">
            @include('CbModulesGen::step2.hookAfterAdd')
        </div>

        <div class="box box-default">
            @include('CbModulesGen::step2.hookBeforeEdit')
        </div>

        <div class="box box-default">
            @include('CbModulesGen::step2.hookAfterEdit')
        </div>

        <div class="box box-default">
            @include('CbModulesGen::step2.hookBeforeDelete')
        </div>

        <div class="box box-default">
            @include('CbModulesGen::step2.hookAfterDelete')
        </div>

    </form>
@endsection