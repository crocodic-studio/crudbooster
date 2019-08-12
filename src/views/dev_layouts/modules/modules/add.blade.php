@extends("crudbooster::dev_layouts.layout")
@section("content")


    <p>
        <a href="{{ route('DeveloperModulesControllerGetIndex') }}"><i class="fa fa-arrow-left"></i> {{ __("cb::cb.back_to_list") }}</a>
    </p>

    <div id="app">

        <div id="box-migration" style="display: none" class="box box-default">
            <div class="box-header">
                <h1 class="box-title">{{ __("cb::cb.create") }} {{ __("cb::cb.migration") }}</h1>
            </div>
            <div class="box-body">

                <div class="callout callout-info">
                    <strong>Info.</strong> For the first, you have to create a table for the module.
                    Learn more about Laravel Migration <a target="_blank" href="https://laravel.com/docs/master/migrations">Click here</a>
                </div>

                <div class="form-group">
                    <label for="">Table Name</label>
                    <input type="text" v-on:keyup.enter="goStructure" name="table_name" required class="form-control">
                </div>

                <div class="row">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Timestamp <a href="#" title="It will add created_at & updated_at column"><i class="fa fa-question-circle"></i></a></label>
                            <input type="checkbox" checked data-toggle="toggle" name="timestamp" value="1">
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Soft Deletes <a href="#" title="It will add deleted_at column"><i class="fa fa-question-circle"></i></a></label>
                            <input type="checkbox" data-toggle="toggle" name="soft_deletes" value="1">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="">Structure</label>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Column Name</th>
                                <th>Type Data</th>
                                <th>Length</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <input type="text" @keyup.enter="addField" class="field_name form-control">
                                    <small>After input you can press enter</small>
                                </td>
                                <td>
                                    <select @keyup.enter="addField" class="type_data form-control">
                                        <option value="string">String</option>
                                        <option value="integer">Integer</option>
                                        <option value="text">Text</option>
                                        <option value="date">Date</option>
                                        <option value="time">Time</option>
                                        <option value="bigIncrements">Big Increments</option>
                                        <option value="increments">Increments</option>
                                        <option value="bigInteger">Big Integer</option>
                                        <option value="dateTime">Date Time</option>
                                        <option value="double">Double</option>
                                        <option value="timeTz">Time (Timezone)</option>
                                        <option value="tinyIncrements">Tiny Increments</option>
                                        <option value="tinyInteger">Tiny Integer</option>
                                        <option value="unsignedBigInteger">Unsigned Big Integer</option>
                                        <option value="unsignedDecimal">Unsigned Decimal</option>
                                        <option value="unsignedInteger">Unsigned Integer</option>
                                        <option value="unsignedMediumInteger">Unsigned Medium Integer</option>
                                        <option value="unsignedSmallInteger">Unsigned Small Integer</option>
                                        <option value="unsignedTinyInteger">Unsigned Tiny Integer</option>
                                        <option value="uuid">UUID</option>
                                        <option value="year">YEAR</option>
                                        <option value="binary">Binary</option>
                                        <option value="boolean">Boolean</option>
                                        <option value="char">Char</option>
                                        <option value="decimal">Decimal</option>
                                        <option value="float">Float</option>
                                        <option value="ipAddress">IP Address</option>
                                        <option value="json">JSON</option>
                                        <option value="jsonb">JSONB</option>
                                        <option value="lineString">Line String</option>
                                        <option value="longText">Long Text</option>
                                        <option value="macAddress">MAC Address</option>
                                        <option value="mediumIncrements">Medium Increments</option>
                                        <option value="mediumInteger">Medium Integer</option>
                                        <option value="mediumText">Medium Text</option>
                                        <option value="morphs">Morphs</option>
                                        <option value="nullableMorphs">Morphs (Nullable)</option>
                                        <option value="uuidMorphs">UUID Morphs</option>
                                        <option value="nullableUuidMorphs">UUID Morphs (Nullable)</option>
                                        <option value="multiLineString">Multi Line String</option>
                                        <option value="multiPoint">Multi Point</option>
                                        <option value="multiPolygon">Multi Polygon</option>
                                        <option value="rememberToken">Remember Token</option>
                                        <option value="smallIncrements">Small Increments</option>
                                        <option value="smallInteger">Small Integer</option>
                                    </select>
                                </td>
                                <td><input type="number" class="length form-control"></td>
                                <td>
                                    <a href="javascript:;" @click="addField" class="btn btn-success"><i class="fa fa-plus"></i></a>
                                </td>
                            </tr>

                            <tr v-for="(field,idx) in listStructure">
                                <td>@{{ field.field_name }}</td>
                                <td>@{{ field.type_data }}</td>
                                <td>@{{ field.length }}</td>
                                <td><a href="javascript:;" @click="removeField(idx)" class="btn btn-danger"><i class="fa fa-trash"></i></a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="box-footer">
                <div align="right">
                    <a href="javascript:;" @click="openBoxModule" class="btn btn-default">Skip <i class="fa fa-arrow-right"></i></a>
                    <a href="javascript:;" @click="saveMigration" class="btn btn-success">Save & Next: Module Generator <i class="fa fa-arrow-right"></i></a>
                </div>
            </div>
        </div>

        @if(request("rebuild"))
            <div class="callout callout-info">
                <strong>Rebuild Note.</strong> If you change table and or name, system will create <strong>a new controller</strong>
            </div>
        @endif

        <div id="box-module" style="display: none" class="box box-default">
            <div class="box-header">
                <h1 class="box-title">{{ (request("rebuild"))?__("cb::cb.rebuild"):__("cb::cb.create") }} {{ __("cb::cb.module") }}</h1>
            </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="">Table Name</label>
                        <select required name="table" class="form-control" v-model="currentTable" @change="changeModuleTable">
                            <option value="" >** Select a table</option>
                            <option v-for="table in listTable" :selected="currentTable==table" :value="table">@{{ table }}</option>
                        </select>
                        <div class="help-block">
                            Select the table that you want to create it module
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Name</label>
                        <input required type="text" placeholder="E.g : Book Manager" value="{{ @$module->name }}" name="name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Icon</label>
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="input-group">
                                    <input type="text" name="icon" value="{{ @$module->icon?:"fa fa-bars" }}" class="form-control" readonly >
                                    <span class="input-group-btn">
                                <button class="btn btn-default" onclick="showIcon(this)" type="button">Choose Icon</button>
                            </span>
                                </div><!-- /input-group -->
                            </div>
                        </div>
                    </div>

                    <div class="form-group">

                        <p align="right">
                            <a href="javascript:;" @click="addColumn" class="btn btn-success"><i class="fa fa-plus"></i> {{ __("cb::cb.add") }} More Column</a>
                        </p>

                        <table id="table-columns" class="table table-striped table-bordered">
                            <thead>
                                <tr class="info">
                                    <th>No</th>
                                    <th>Column</th>
                                    <th width="400px">Type</th>
                                    <th>Configuration</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <?php
                            $dirPath = base_path("vendor/crocodicstudio/crudbooster/src/types");
                            $types = scandir($dirPath);
                            ?>
                            <tbody>
                                <tr v-for="(item, idx) in listColumns">
                                    <td align="center">
                                        @{{ idx+1 }}
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <label for="">Label</label>
                                            <input type="text" v-model="item.column_label" class="form-control column_label">
                                            <div class="help-block">This box is for column label</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Field</label>
                                            <input type="text" v-model="item.column_field" class="form-control column_field">
                                            <div class="help-block">Fill this box with table column of this column context</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Help Text</label>
                                            <input type="text" v-model="item.column_help" class="form-control column_help">
                                            <div class="help-block">Fill this box with guide to help user to input the data</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <label for="">Type</label>
                                            <select v-model="item.column_type" class="column_type form-control">
                                                @foreach($types as $type)
                                                    @if($type != "." && $type != ".." && is_dir($dirPath.'/'.$type))
                                                        <option value="{{ $type }}">{{ $type }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>

                                        <div v-if="item.column_type == 'select_table'" class="select_table_configuration mt-10">
                                            <div class="form-group">
                                                <label for="">Table <sup title="Required" class="text-danger">(required)</sup></label>
                                                <select class="form-control select-table" v-model="item.column_option_table" @change="changeColumnOptionTable($event, idx)">
                                                    <option value="" >** Select a table</option>
                                                    <option v-for="table in listTable" :value="table">@{{ table }}</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="">Value Option <sup title="Required" class="text-danger">(required)</sup></label>
                                                <select class="form-control" @change="setValueOption($event, idx)">
                                                    <option value="" >** Select a column</option>
                                                    <option v-for="column in item.listTableColumns" :selected="column.primary_key === true" :value="column.column" >@{{ column.column }}</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="">Display Option <sup title="Required" class="text-danger">(required)</sup></label>
                                                <select class="form-control" @change="setDisplayOption($event, idx)">
                                                    <option value="" >** Select a column</option>
                                                    <option v-for="column in item.listTableColumns" :selected="column.display === true" :value="column.column" >@{{ column.column }}</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="">SQL RAW Condition <sup class="text-primary">(optional)</sup></label>
                                                <input type="text" class="form-control" v-model="item.column_option_sql_condition">
                                                <div class="help-block">You may enter query condition in here. <br> E.g : <code>status = 'Active'</code></div>
                                            </div>
                                        </div>

                                        <div v-if="item.column_type == 'select_query'" class="mt-10 select_query_configuration">
                                            <div class="form-group">
                                                <label for="">SQL Raw Query <sup class="text-danger">(required)</sup></label>
                                                <textarea v-model="item.column_sql_query" placeholder="E.g: select id as key, name as label from category..." class="form-control" rows="3"></textarea>
                                                <div class="help-block">Select query should contain key and label. <br> Eg: <code>select id <strong>as `key`</strong>, name <strong>as `label`</strong> from categories</code>. Make sure the single quote also.</div>
                                            </div>
                                        </div>

                                        <div v-if="item.column_type == 'select_option' || item.column_type == 'checkbox' || item.column_type == 'radio'" class="options_configuration mt-10">
                                            <div class="form-group">
                                                <label for="">Options</label>
                                                <p>
                                                    <a href="javascript:;" @click="addColumnOption(idx)" class="btn btn-sm btn-info"><i class="fa fa-plus"></i> Add Option</a>
                                                </p>
                                                <table class="table table-striped table-bordered">
                                                    <thead>
                                                        <tr class="warning">
                                                            <th>No</th>
                                                            <th>Key <sup class="text-danger">(required)</sup></th>
                                                            <th>Label <sup class="text-danger">(required)</sup></th>
                                                            <th> </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(option, oidx) in item.column_options">
                                                            <td>@{{ oidx + 1 }}</td>
                                                            <td><input type="text" placeholder="E.g: foo" @keyup.enter="addColumnOption(idx)" v-model="option.key" class="form-control"></td>
                                                            <td><input type="text" placeholder="E.g: Bar" @keyup.enter="addColumnOption(idx)" v-model="option.label" class="form-control"></td>
                                                            <td>
                                                                <a href="javascript:;" class="btn btn-sm btn-danger" title="Click here to remove" @click="removeColumnOption(idx, oidx)"><i class="fa fa-trash"></i></a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <label for="">Configuration</label>

                                            <input type="checkbox" v-model="item.column_mandatory" class="big-checkbox column_mandatory"> Mandatory
                                            <p></p>
                                            <input type="checkbox" v-model="item.column_browse" class="big-checkbox column_browse"> Display On Browse
                                            <p></p>
                                            <input type="checkbox" v-model="item.column_add" class="big-checkbox column_add"> Display On Add
                                            <p></p>
                                            <input type="checkbox" v-model="item.column_edit" class="big-checkbox column_edit"> Display On Edit
                                            <p></p>
                                            <input type="checkbox" v-model="item.column_detail" class="big-checkbox column_detail"> Display On Detail
                                        </div>
                                    </td>
                                    <td>
                                        <a href="javascript:;" @click="removeColumn(idx)" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="pull-left">
                        @if(!request("rebuild"))
                            <a href="javascript:;" @click="openBoxMigration" class="btn btn-default"><i class="fa fa-arrow-left"></i> Migration</a>
                        @endif
                    </div>
                    <div class="pull-right">
                        <a href="javascript:;" @click="saveModuleGenerate" class="btn btn-success"><i class="fa fa-wrench"></i> {{ (request("rebuild"))?__("cb::cb.rebuild"):__("cb::cb.create") }} {{ __("cb::cb.module") }}</a>
                    </div>
                </div>
        </div>

    </div><!--end app-->


    <div class="modal" id="modal-loading">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div align="center">
                        <h3><i class="fa fa-spin fa-spinner"></i></h3>
                        <p>Please wait while loading...</p>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    @push('bottom')
        <script src="{{ cbAsset("adminlte/bower_components/bootstrap-toggle/bootstrap-toggle.min.js") }}"></script>
        <script src="{{ cbAsset("js/vue.min.js") }}"></script>
        <script src="{{ cbAsset("js/axios.min.js") }}"></script>
        <script>
            function showLoading() {
                $("#modal-loading").modal("show")
            }
            function hideLoading() {
                $("#modal-loading").modal("hide")
            }

            const app = new Vue({
                el: '#app',
                data: {
                    listStructure:[],
                    listTable: [],
                    listColumns: [],
                    currentTable:""
                },
                mounted() {
                    this.addDefaultColumnMigration()
                    @if(request("rebuild"))
                        this.openBoxModule()
                    @else
                        this.openBoxMigration()
                    @endif

                },
                methods: {
                    addColumnOption: function(idx) {

                        this.listColumns[idx].column_options.push({
                            key: "",
                            label: ""
                        })
                    },
                    removeColumnOption: function(idx, oidx) {

                        this.listColumns[idx].column_options.splice(oidx, 1)
                    },
                    setValueOption: function(event, idx) {
                        this.listColumns[idx].column_option_value = event.target.value
                    },
                    setDisplayOption: function(event, idx) {
                        this.listColumns[idx].column_option_display = event.target.value
                    },
                    changeColumnOptionTable: function(event, idx) {
                        let table = event.target.value;
                        if(table) {
                            this.getListTableColumn(table, idx)
                        }
                    },
                    getListTableColumn: function(table, idx) {
                        axios.get("{{ route("DeveloperModulesControllerGetAllColumn") }}/"+table)
                            .then(response=>{
                                this.listColumns[idx].listTableColumns = response.data
                                response.data.forEach(data=>{
                                    if(data.primary_key === true) {
                                        this.listColumns[idx].column_option_value = data.column
                                    }

                                    if(data.display === true) {
                                        this.listColumns[idx].column_option_display = data.column
                                    }
                                })
                            })
                            .catch(err=>{
                                swal("Oops","Something went wrong while load list table column","warning")
                            })
                    },
                    changeModuleTable: function(idx) {
                        showLoading()
                        let table = this.currentTable
                        let title = table
                            .replace("_"," ")
                            .replace(/^(.)|\s+(.)/g, function ($1) {
                                return $1.toUpperCase()
                            });
                        if( $("#box-module input[name=name]").val() == "") {
                            $("#box-module input[name=name]").val(title)
                        }

                        axios.get("{{ route("DeveloperModulesControllerGetColumns") }}/"+table+"?modules_id={{ request("modules_id") }}")
                            .then(response=>{
                                let data = response.data
                                if(data) {
                                    this.listColumns = []
                                    data.forEach(item=>{
                                        this.listColumns.push(item)
                                        if(item.column_option_table) {
                                            this.getListTableColumn(item.column_option_table, this.listColumns.length-1)
                                        }
                                    })
                                }
                                hideLoading()
                            })
                            .catch(err=>{
                                swal("Oops","Something went wrong while get columns","warning")
                                hideLoading()
                            })
                    },
                    removeColumn: function(idx) {
                      this.listColumns.splice(idx,1)
                    },
                    addColumn: function() {
                        this.listColumns.push({
                            column_label: "",
                            column_field: "",
                            column_type: "text",
                            column_option_table: "",
                            column_option_value: "",
                            column_option_display: "",
                            column_option_sql_condition: "",
                            column_options:[],
                            column_sql_query: "",
                            column_help: "",
                            column_mandatory: "on",
                            column_browse: "on",
                            column_detail: "on",
                            column_edit: "on",
                            column_add: "on",
                            listTableColumns: []
                        })

                        $("html, body").animate({scrollTop: $("#table-columns").height() + 300 })
                    },

                    submitModuleGenerate: function(url, table, icon, name) {
                        if(table && icon && name) {
                            showLoading()
                            axios.post(url, {
                                _token: "{{ csrf_token() }}",
                                rebuild: "{{ request("rebuild")?1:0 }}",
                                table: table,
                                icon: icon,
                                name: name,
                                columns: this.listColumns
                            })
                                .then(response=>{
                                    hideLoading()

                                    if(response.data.status) {
                                        swal({
                                            title:"Module Has Been Created!",
                                            text: response.data.message,
                                            icon:"success",
                                            closeOnEsc: false,
                                            closeOnClickOutside: false
                                        }).then(value=>{
                                            location.href = "{{ route("DeveloperModulesControllerGetIndex") }}";
                                        })

                                    }else{
                                        swal("Oops", response.data.message ,"warning")
                                    }
                                })
                                .catch(err=>{
                                    hideLoading()
                                    swal("Oops","Something went wrong while save module","warning")
                                })
                        }else{
                            swal("Oops","{{ __("cb::cb.please_complete_the_form") }}","warning")
                        }
                    },
                    saveModuleGenerate: function() {
                        let url = "{{ route('DeveloperModulesControllerPostAddSave') }}";
                        let table = $("#box-module select[name=table]").val()
                        let icon = $("#box-module input[name=icon]").val()
                        let name = $("#box-module input[name=name]").val()

                        axios.post("{{ route("DeveloperModulesControllerPostCheckExistModule") }}",{
                            _token:"{{ csrf_token() }}",
                            table: table,
                            name: name
                        })
                        .then(response=>{
                            if(response.data.status) {
                                showConfirmation("Are you sure want to rebuild?","This module is already exists, if you press OK system will rebuild it.", ()=>{
                                    this.submitModuleGenerate(url, table, icon, name)
                                })
                            }else{
                                this.submitModuleGenerate(url, table, icon, name)
                            }
                        })
                        .catch(err=>{
                            swal("Oops","Something went wrong while check the module","warning")
                        })
                    },
                    getTables: function() {
                        axios.get("{{ route("DeveloperModulesControllerGetTables") }}")
                            .then(response=>{
                                this.listTable = response.data

                                @if(request("rebuild"))
                                    this.currentTable = "{{ request("rebuild") }}"
                                    this.changeModuleTable()
                                @endif
                            })
                            .catch(err=>{
                                swal("Oops", "Something went wrong while load tables", "warning")
                            })
                    },
                    saveMigration: function() {

                        let table_name = $("#box-migration input[name=table_name]").val()
                        let timestamp = $("#box-migration input[name=timestamp]:checked").val()
                        let soft_deletes = $("#box-migration input[name=soft_deletes]:checked").val()

                        if(this.listStructure.length > 1 && table_name) {
                            showLoading()

                            axios.post("{{ route("DeveloperModulesControllerPostCreateMigration") }}",{
                                _token: "{{ csrf_token() }}",
                                table_name: table_name,
                                timestamp: timestamp,
                                soft_deletes: soft_deletes,
                                structures: this.listStructure
                            })
                                .then(response=>{
                                    if(response.data.status) {
                                        swal("Success", response.data.message, "success")
                                    }else{
                                        swal("Oops", response.data.message, "warning")
                                    }

                                    this.addDefaultColumnMigration()
                                    $("input[name=table_name]").val(null)

                                    hideLoading()

                                    this.getTables()

                                    this.openBoxModule()
                                })
                                .catch(err=>{
                                    swal("Oops", "Someting went wrong!", "warning")
                                    hideLoading()
                            })
                        }else{
                            swal("Oops","{{ __("cb::cb.please_complete_the_form") }}","warning")
                        }
                    },
                    goStructure: function() {
                      $(".field_name").focus()
                    },
                    addField: function() {
                        if( $(".field_name").val() && $(".type_data").val() ) {
                            this.listStructure.push({
                                field_name: $(".field_name").val(),
                                type_data: $(".type_data").val(),
                                length: $(".length").val()
                            })
                            $(".field_name,.type_data,.length").val("")
                            $(".type_data").val("string")
                            $(".field_name").focus()
                        }
                    },
                    removeField: function(idx) {
                        this.listStructure.splice(idx, 1)
                    },
                    addDefaultColumnMigration: function() {
                        this.listStructure = []
                        this.listStructure.push({
                            field_name: "id",
                            type_data: "bigIncrements",
                            length: null
                        })
                    },
                    openBoxModule: function() {
                        $("#app .box").hide()
                        $("#box-module").show()
                        this.getTables()
                    },
                    openBoxMigration: function() {
                        $("#app .box").hide()
                        $("#box-migration").show()
                    }
                }
            });

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
        <link rel="stylesheet" href="{{ cbAsset("adminlte/bower_components/bootstrap-toggle/bootstrap-toggle.min.css") }}">
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
            #box-module table tr th {
                padding: 5px;
                text-align: center;
            }
            #box-module table tr td {
                padding: 5px
            }
            .big-checkbox {
                -ms-transform: scale(1.5); /* IE */
                -moz-transform: scale(1.5); /* FF */
                -webkit-transform: scale(1.5); /* Safari and Chrome */
                -o-transform: scale(1.5); /* Opera */
                transform: scale(1.5);
                cursor:pointer;
            }
        </style>
    @endpush

    <div class="modal fade" id="modal-fontawesome">
        <div class="modal-dialog modal-lg">
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