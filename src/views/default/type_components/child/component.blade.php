<?php
$name = str_slug($label, '');
?>
@push('bottom')
    <script type="text/javascript">
        $(function () {
            $('#form-group-{{$name}} .select2').select2();
        })
    </script>
@endpush
<div class='form-group {{$header_group_class}}' id='form-group-{{$name}}'>

    @if($formInput['columns'])
        <div class="col-sm-12">

            <div id='panel-form-{{$name}}' class="panel panel-default">
                <div class="panel-heading">
                    <i class='fa fa-bars'></i> {{$label}}
                </div>
                <div class="panel-body">

                    <div class='row'>
                        <div class='col-sm-10'>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <i class="fa fa-pencil-square-o"></i> {{cbTrans("text_form")}}</div>
                                <div class="panel-body child-form-area">
                                    @foreach($formInput['columns'] as $col)
                                        <?php $name_column = $name.$col['name'];?>
                                        <div class='form-group'>
                                            @if($col['type']!='hidden')
                                                <label class="control-label col-sm-2">{{$col['label']}}
                                                    @if(!empty($col['required']))
                                                        <span class="text-danger" title="{{cbTrans('this_field_is_required')}}">*</span>
                                                    @endif
                                                </label>
                                            @endif
                                            <div class="col-sm-10">
                                                @if($col['type']=='text')
                                                    <input id='{{$name_column}}'
                                                           type='text'
                                                           {{ ($col['max'])?"maxlength='{$col['max']}'":"" }}
                                                           name='child-{{$col["name"]}}'
                                                           class='form-control {{$col['required']?"required":""}}'
                                                           {{($col['readonly']===true)?"readonly":""}}
                                                    />
                                                @elseif($col['type']=='radio')
                                                    @include('crudbooster::default.type_components.child.partials.dataenum', ['dataEnum' => $col['dataenum'] ?: ''])
                                                @elseif($col['type']=='datamodal')
                                                    @include('crudbooster::default.type_components.child.partials.dataModal', ['name' => $name, 'col' => $col])
                                                @elseif($col['type']=='number')
                                                    <input id='{{$name_column}}' type='number'
                                                           {{ ($col['min'])?"min='$col[min]'":"" }} {{ ($col['max'])?"max='{$col['max']}'":"" }} name='child-{{$col["name"]}}'
                                                           class='form-control {{$col['required']?"required":""}}'
                                                            {{($col['readonly']===true)?"readonly":""}}
                                                    />
                                                @elseif($col['type']=='textarea')
                                                    <textarea id='{{$name_column}}' name='child-{{$col["name"]}}'
                                                              class='form-control {{$col['required']?"required":""}}' {{($col['readonly']===true)?"readonly":""}} ></textarea>
                                                @elseif($col['type']=='upload')
                                                    @include('crudbooster::default.type_components.child.partials.upload', ['name' => $name, 'col' => $col])
                                                @elseif($col['type']=='select')
                                                    @include('crudbooster::default.type_components.child.partials.select', ['name' => $name, 'col' => $col])

                                                @elseif($col['type']=='hidden')
                                                    <input type="{{$col['type']}}" id="{{$name.$col["name"]}}"
                                                           name="child-{{$name.$col["name"]}}"
                                                           value="{{$col["value"]}}">
                                                @endif

                                                @if($col['help'])
                                                    <div class='help-block'>
                                                        {{$col['help']}}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                     @include('crudbooster::default.type_components.child.partials.formula')

                                    @endforeach

                                    @include('crudbooster::default.type_components.child.partials.script')

                                </div>
                                <div class="panel-footer" align="right">
                                    <input type='button' class='btn btn-default' id="btn-reset-form-{{$name}}"
                                           onclick="resetForm{{$name}}()" value='{{cbTrans("button_reset")}}'/>
                                    <input type='button' id='btn-add-table-{{$name}}' class='btn btn-primary'
                                           onclick="addToTable{{$name}}()" value='{{cbTrans("button_add_to_table")}}'/>
                                </div>
                            </div>
                        </div>
                    </div>



                    @include('crudbooster::default.type_components.child.partials.details_table')



                </div>
                <!-- /.box-body -->
            </div>


        </div>


    @else

        <div style="border:1px dashed #c41300;padding:20px;margin:20px">
            <span style="background: yellow;color: black;font-weight: bold">CHILD {{$name}} : COLUMNS ATTRIBUTE IS MISSING !</span>
            <p>You need to set the "columns" attribute manually</p>
        </div>
    @endif
</div>
