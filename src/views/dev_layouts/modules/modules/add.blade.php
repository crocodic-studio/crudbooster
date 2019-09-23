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
                                <tr v-if="!listColumns.length">
                                    <td colspan="5" align="center">
                                        {{ cbLang("there_is_no_data_yet") }} <a href="javascript:;" @click="addColumn">Click here</a> to add new
                                    </td>
                                </tr>
                                <tr v-if="listColumns.length" v-for="(item, idx) in listColumns">
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
                                            <select v-model="item.column_type" @change="changeTypeColumn($event, idx)" class="column_type form-control">
                                                @foreach($types as $type)
                                                    @if($type != "." && $type != ".." && is_dir($dirPath.'/'.$type))
                                                        <?php $filterable = (file_exists($dirPath."/filter.blade.php"))?1:0; ?>
                                                        <option data-filterable="{{ $filterable }}" value="{{ $type }}">{{ $type }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>

                                        <div v-if="item.column_type=='money'" class="text_configuration mt-10">
                                            <table class="table table-bordered table-striped">
                                                <tr>
                                                    <td width="40%">Prefix</td>
                                                    <td><input type="text" class="form-control" v-model="item.column_money_prefix">
                                                        <small class="text-muted">The prefix will be shown on index <br>E.g: Rp</small>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Precision</td>
                                                    <td><input type="number" v-model="item.column_money_precision" class="form-control"></td>
                                                </tr>
                                                <tr>
                                                    <td>Thousand Separator</td>
                                                    <td><input type="text" v-model="item.column_money_thousand_separator" class="form-control"></td>
                                                </tr>
                                                <tr>
                                                    <td>Decimal Separator</td>
                                                    <td><input type="text" v-model="item.column_money_decimal_separator" class="form-control"></td>
                                                </tr>
                                            </table>
                                        </div>

                                        <div v-if="item.column_type=='text'||item.column_type=='text_area'||item.column_type=='wysiwyg'" class="text_configuration mt-10">
                                            <table class="table table-bordered table-striped">
                                                <tr>
                                                    <td width="40%">Index Display Limit</td>
                                                    <td><input type="number" class="form-control" v-model="item.column_text_display_limit">
                                                        <small class="text-muted">To limit chars on index display</small>
                                                    </td>
                                                </tr>
                                                <tr v-if="item.column_type!='wysiwyg'">
                                                    <td>Max Character</td>
                                                    <td><input type="number" class="form-control" v-model="item.column_text_max"></td>
                                                </tr>
                                                <tr v-if="item.column_type!='wysiwyg'">
                                                    <td>Min Character</td>
                                                    <td><input type="number" class="form-control" v-model="item.column_text_min"></td>
                                                </tr>
                                            </table>
                                        </div>

                                        <div v-if="item.column_type=='date' || item.column_type=='datetime'" class="date_configuration mt-10">
                                            <table class="table table-bordered table-striped">
                                                <tr><td width="30%">Format</td><td><input type="text" class="form-control" v-model="item.column_date_format">
                                                    <small v-if="item.column_type=='datetime'" class="text-muted">E.g: Y-m-d H:i:s. This format is following
                                                        <a target="_blank" href="https://www.php.net/manual/en/function.date.php">PHP Date Format</a></small>
                                                    <small v-if="item.column_type=='date'" class="text-muted">E.g: Y-m-d. This format is following
                                                        <a target="_blank" href="https://www.php.net/manual/en/function.date.php">PHP Date Format</a></small>
                                                    </td></tr>
                                            </table>
                                        </div>

                                        <div v-if="item.column_type == 'image' || item.column_type == 'file'" class="image_configuration mt-10">
                                            <table class="table table-bordered table-striped">
                                                <tbody>
                                                    <tr>
                                                        <td width="40%">Encrypt Filename</td>
                                                        <td><input type="checkbox" v-model="item.column_file_encrypt" class="big-checkbox checkbox"></td>
                                                    </tr>
                                                    <tr v-if="item.column_type == 'image'">
                                                        <td>Resize Width</td>
                                                        <td><input type="number" class="form-control" v-model="item.column_image_width" placeholder="Width in pixel"></td>
                                                    </tr>
                                                    <tr v-if="item.column_type == 'image'">
                                                        <td>Resize Height</td>
                                                        <td><input type="number" class="form-control" v-model="item.column_image_height" placeholder="Height in pixel">
                                                            <small class="text-muted">Fill blank this box to auto resize of height</small>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
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
                                                <label for="">Foreign <sup title="Required" class="text-primary">(Optional)</sup></label>
                                                <select class="form-control" @change="setForeignSelect($event, idx)">
                                                    <option value="" >** Select a column</option>
                                                    <option v-for="column in listTableColumns" :selected="item.column_foreign == column.column" :value="column.column" >@{{ column.column }}</option>
                                                </select>
                                                <div class="help-block">If you need to set this combo auto filtered, select the foreign key / parent select_table. Study Case: Province->City</div>
                                            </div>

                                            <div class="form-group">
                                                <label for="">SQL RAW Condition <sup class="text-primary">(optional)</sup></label>
                                                <input type="text" class="form-control" v-model="item.column_option_sql_condition">
                                                <div class="help-block">You may enter query condition in here. <br> E.g : <code>status = 'Active'</code></div>
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
                                            <p></p>
                                            <input type="checkbox" v-model="item.column_filterable" class="big-checkbox column_filterable"> Filter
                                            <a href="javascript:;" title="Show the filter feature on the table module&#013;*Note: not all types have a filter"><i class="fa fa-question-circle"></i></a>
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


    @push('bottom')
        <script src="{{ cbAsset("js/bootstrap-toggle/bootstrap-toggle.min.js") }}"></script>
        <script src="{{ cbAsset("js/vue.min.js") }}"></script>
        <script src="{{ cbAsset("js/axios.min.js") }}"></script>
        <script>
            const app = new Vue({
                el: '#app',
                data: {
                    listStructure:[],
                    listTable: [],
                    listColumns: [],
                    listTableColumns: [],
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
                    setForeignSelect: function(event, idx) {
                        this.listColumns[idx].column_foreign = event.target.value
                    },
                    changeTypeColumn: function(event, idx) {
                        let type = this.listColumns[idx].column_type
                        if(type == "wysiwyg" || type == "text_area") {
                            this.listColumns[idx].column_text_max = ""
                            this.listColumns[idx].column_text_min = ""
                        }else if(type == "text") {
                            this.listColumns[idx].column_text_min = 0;
                            this.listColumns[idx].column_text_max = 255;
                        }
                    },
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
                    getListTableColumnCurrentTable: function(table) {
                        axios.get("{{ route("DeveloperModulesControllerGetAllColumn") }}/"+table)
                            .then(response=>{
                                this.listTableColumns = response.data
                            })
                            .catch(err=>{
                                swal("Oops","Something went wrong while load list table column","warning")
                            })
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

                        this.getListTableColumnCurrentTable(table)
                    },
                    removeColumn: function(idx) {
                      this.listColumns.splice(idx,1)
                    },
                    addColumn: function() {
                        this.listColumns.push({
                            column_label: "",
                            column_field: "",
                            column_type: "text",
                            column_file_encrypt: "on",
                            column_image_width: "",
                            column_image_height: "",
                            column_money_prefix: "",
                            column_money_precision: "",
                            column_money_thousand_separator: "",
                            column_money_decimal_separator:"",
                            column_text_display_limit: "",
                            column_text_max: "",
                            column_text_min:"",
                            column_date_format: "",
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
                            column_filterable: "",
                            column_foreign: "",
                            listTableColumns: []
                        })

                        $("html, body").animate({scrollTop: $("#table-columns").height() + 300 })
                    },

                    submitModuleGenerate: function(url, table, icon, name) {
                        if(table && icon && name) {
                            showLoading()

                            if( this.listColumns.length > 0) {
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
                            } else {
                                swal("Oops","{{ cbLang("please_complete_the_form")  }}","warning")
                            }
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
        <link rel="stylesheet" href="{{ cbAsset("js/bootstrap-toggle/bootstrap-toggle.min.css") }}">
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
                                <div class="panel-heading" role="tab" id="heading{{ slug($f['label'])}}">
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