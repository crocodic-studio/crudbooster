@extends("crudbooster::admin_template")
@section("content")
@push('head')
<link rel='stylesheet' href='<?php echo asset("vendor/crudbooster/assets/select2/dist/css/select2.min.css")?>'/>
<style>
	.select2-container--default .select2-selection--single {border-radius: 0px !important}
	.select2-container .select2-selection--single {height: 35px}
</style>
@endpush
@push('bottom')
<script src='<?php echo asset("vendor/crudbooster/assets/select2/dist/js/select2.full.min.js")?>'></script>
<script>
    $(function() {
        $('.select2').select2();        
    })
</script>
@endpush

<ul class="nav nav-tabs">
  <li role="presentation"><a href="{{Route('AdminModulesControllerGetStep1',['id'=>$id])}}"><i class='fa fa-info'></i> Step 1 - Module Information</a></li>
  <li role="presentation" class="active"><a href="{{Route('AdminModulesControllerGetStep2',['id'=>$id])}}"><i class='fa fa-table'></i> Step 2 - Table Display</a></li>
  <li role="presentation"><a href="{{Route('AdminModulesControllerGetStep3',['id'=>$id])}}"><i class='fa fa-plus-square-o'></i> Step 3 - Form Display</a></li>
  <li role="presentation"><a href="{{Route('AdminModulesControllerGetStep4',['id'=>$id])}}"><i class='fa fa-wrench'></i> Step 4 - Configuration</a></li>
</ul>
@push('head')
<style>
    .table-display tbody tr td {
        position:relative;
    }
    .sub {
        position:absolute;
        top:inherit;
        left:inherit;
        padding:0 0 0 0;
        list-style-type:none;
        height:180px;
        overflow:auto;
        z-index: 1;
    }
    .sub li {
        padding:5px;
        background:#eae9e8;
        cursor:pointer;
        display:block;
        width:180px;
    }
    .sub li:hover {
        background:#ECF0F5;
    }

    .btn-drag {
        cursor: move;
    }
</style>
@endpush

@push('bottom')
<script>
    var columns = {!! json_encode($columns) !!};
    var tables = {!! json_encode($table_list) !!};

    function ucwords (str) {
        return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
            return $1.toUpperCase();
        });
    }

    function showNameSuggest(t) {     
        t = $(t);

        t.next("ul").remove();                 
        var list = '';
        $.each(columns,function(i,obj) {    
            list += "<li>"+obj+"</li>";        
        });

        t.after("<ul class='sub'>"+list+"</ul>"); 
    }

    function showNameSuggestLike(t) {
        t = $(t);

        var v = t.val();
        t.next("ul").remove();   
        if(!v) return false;

        var list = '';
        $.each(columns,function(i,obj) {                                
            if(obj.includes(v.toLowerCase())) {            
                list += "<li>"+obj+"</li>";        
            }                
        });

        t.after("<ul class='sub'>"+list+"</ul>"); 
    }       

    function showColumnSuggest(t) {                
        t = $(t);
        t.next("ul").remove();            
        
        var list = '';
        $.each(columns,function(i,obj) {
            obj = obj.replace('id_','');
            obj = ucwords(obj.replace('_',' '));
            list += "<li>"+obj+"</li>";                
        });

        t.after("<ul class='sub'>"+list+"</ul>");
    }

    function showColumnSuggestLike(t) {
        t = $(t);
        var v = t.val();            
        
        t.next("ul").remove();
        if(!v) return false;
        
        var list = '';
        $.each(columns,function(i,obj) {
            
            if(obj.includes(v.toLowerCase())) {
                obj = obj.replace('id_','');
                obj = ucwords(obj.replace('_',' '));

                list += "<li>"+obj+"</li>";    
            }                
        });

        t.after("<ul class='sub'>"+list+"</ul>");
    }

    function showTable(t) {
        t = $(t);
        t.next("ul").remove();
        var list = '';
        $.each(tables,function(i,obj) {
            list += "<li>"+obj+"</li>";                
        });

        t.after("<ul class='sub'>"+list+"</ul>");     
    }

    function showTableLike(t) {
        t = $(t);
        var v = t.val();            

        t.next("ul").remove();
        if(!v) return false;
        
        var list = '';
        $.each(tables,function(i,obj) {
            if(obj.includes(v.toLowerCase())) {
                list += "<li>"+obj+"</li>";    
            }                
        });

        t.after("<ul class='sub'>"+list+"</ul>");
    }

    function showTableFieldLike(t) {
        t         = $(t);
        var table = t.parent().parent().find('.join_table').val();            
        var v     = t.val();
        
        t.next("ul").remove();   

        if(!table) return false;
        if(!v) return false;        

        t.after("<ul class='sub'><li><i class='fa fa-spin fa-spinner'></i> Loading...</li></ul>");

        $.get("{{CRUDBooster::mainpath('table-columns')}}/"+table,function(response) { 
            t.next("ul").remove();                              
            var list = '';
            $.each(response,function(i,obj) {
                if(obj.includes(v.toLowerCase())) {
                    list += "<li>"+obj+"</li>";    
                }               
            });
            t.after("<ul class='sub'>"+list+"</ul>");
        });
    }

    function showTableField(t) {
        t = $(t);
        var table = t.parent().parent().find('.join_table').val();            
        var v = t.val();
        
        if(!table) return false;

        t.after("<ul class='sub'><li><i class='fa fa-spin fa-spinner'></i> Loading...</li></ul>");

        $.get("{{CRUDBooster::mainpath('table-columns')}}/"+table,function(response) { 
            t.next("ul").remove();                           
            var list = '';
            $.each(response,function(i,obj) {
                list += "<li>"+obj+"</li>";               
            });
            t.after("<ul class='sub'>"+list+"</ul>");
        });
    }

    $(function() {
        

        $(document).on('click','.btn-plus',function() {            
            var tr_parent = $(this).parent().parent('tr');
            var clone = $('#tr-sample').clone();
            clone.removeAttr('id');
            tr_parent.after(clone);
            $('.table-display tr').not('#tr-sample').show();
        })

        //init row 
        $('.btn-plus').last().click();

        $(document).mouseup(function (e)
        {
            var container = $(".sub");
            if (!container.is(e.target) 
                && container.has(e.target).length === 0) 
            {
                container.hide();
            }
        });

        $(document).on('click','.sub li',function() {
            var v = $(this).text();
            $(this).parent('ul').prev('input[type=text]').val(v);
            $(this).parent('ul').remove();
        })         

        $(document).on('click','.table-display .btn-delete',function() {
            $(this).parent().parent().remove();
        })

        $(document).on('click','.table-display .btn-up',function() {
            var tr = $(this).parent().parent();
            var trPrev = tr.prev('tr');
            if(trPrev.length != 0) {
                
                tr.prev('tr').before(tr.clone());
                tr.remove();    
            }            
        })

        $(document).on('click','.table-display .btn-down',function() {
            var tr = $(this).parent().parent();
            var trPrev = tr.next('tr');
            if(trPrev.length != 0) {
                
                tr.next('tr').after(tr.clone());
                tr.remove();    
            }            
        })

        $(document).on('change','.is_image',function() {
            var tr = $(this).parent().parent();
            if($(this).val() == 1) {
                tr.find('.is_download').val(0);
            }            
        })

        $(document).on('change','.is_download',function() {
            var tr = $(this).parent().parent();
            if($(this).val() == 1) {
                tr.find('.is_image').val(0);
            }            
        })

    })
</script>

@endpush

<form method="post" action="{{Route('AdminModulesControllerPostStep3')}}">

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">Table Display</h3>
    </div>
    <div class="box-body">    
    
        <input type="hidden" name="_token" value="{{csrf_token()}}">
        <input type="hidden" name="id" value="{{$id}}" >
        
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
                        <td><input value='{{$c["label"]}}' type='text' name='column[]' onclick='showColumnSuggest(this)' onKeyUp='showColumnSuggestLike(this)' placeholder='Column Name' class='column form-control notfocus' value=''/></td>
                        <td><input value='{{$c["name"]}}' type='text' name='name[]' onclick='showNameSuggest(this)' onKeyUp='showNameSuggestLike(this)' placeholder='Field Name' class='name form-control notfocus' value=''/></td>                        
                        <td><input value='{{$c["width"]?:0}}' type='number' name='width[]' class='form-control'/></td>
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
                            <a href="javascript:void(0)" class="btn btn-primary btn-callback"><i class="fa fa-pencil"></i> Callback</a>
                            <input type="hidden" class="input-callback" name="callback[]" value="{{$c['callback']}}">
                        </td>
                        <td>
                            <a href="javascript:void(0)" class="btn btn-info btn-plus"><i class='fa fa-plus'></i></a>
                            <a href="javascript:void(0)" class="btn btn-danger btn-delete"><i class='fa fa-trash'></i></a>
                            <a href="javascript:void(0)" class="btn btn-success btn-up"><i class='fa fa-arrow-up'></i></a>
                            <a href="javascript:void(0)" class="btn btn-success btn-down"><i class='fa fa-arrow-down'></i></a>
                        </td>
                    </tr>                    
                @endforeach          
                @endif  

                <tr id="tr-sample" style="display:none">
                    <td><input type='text' name='column[]' onclick='showColumnSuggest(this)' onKeyUp='showColumnSuggestLike(this)' placeholder='Column Name' class='column form-control notfocus' value=''/></td>
                    <td><input type='text' name='name[]' onclick='showNameSuggest(this)' onKeyUp='showNameSuggestLike(this)' placeholder='Field Name' class='name form-control notfocus' value=''/></td>
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
                        <a href="javascript:void(0)" class="btn btn-primary btn-callback"><i class="fa fa-pencil"></i> Callback</a>
                        <input type="hidden" class="input-callback" name="callback[]">
                    </td>
                    <td>
                        <a href="javascript:void(0)" class="btn btn-info btn-plus"><i class='fa fa-plus'></i></a>
                        <a href="javascript:void(0)" class="btn btn-danger btn-delete"><i class='fa fa-trash'></i></a>
                        <a href="javascript:void(0)" class="btn btn-success btn-up"><i class='fa fa-arrow-up'></i></a>
                        <a href="javascript:void(0)" class="btn btn-success btn-down"><i class='fa fa-arrow-down'></i></a>
                    </td>
                </tr>   

            </tbody>
        </table>

    </div>
    <div class="box-footer">
        <div align="right">
            <button type="button" onclick="location.href='{{CRUDBooster::mainpath('step1').'/'.$id}}'" class="btn btn-default">&laquo; Back</button>
            
            <button class="btn btn-success btn-sm" type="submit"><i class="fa fa-save"></i> Save Module</button>
        
        </div>
    </div>
    
</div>

@push('bottom')
    <script src='<?php echo asset("vendor/crudbooster/assets/codemirror/lib/codemirror.js")?>'></script>    
    
    <script src="<?php echo asset("vendor/crudbooster/assets/codemirror/addon/edit/matchbrackets.js")?>"></script>
    <script src="<?php echo asset("vendor/crudbooster/assets/codemirror/mode/htmlmixed/htmlmixed.js")?>"></script>
    <script src="<?php echo asset("vendor/crudbooster/assets/codemirror/mode/xml/xml.js")?>"></script>
    <script src="<?php echo asset("vendor/crudbooster/assets/codemirror/mode/javascript/javascript.js")?>"></script>
    <script src="<?php echo asset("vendor/crudbooster/assets/codemirror/mode/css/css.js")?>"></script>
    <script src="<?php echo asset("vendor/crudbooster/assets/codemirror/mode/clike/clike.js")?>"></script>
    <script src="<?php echo asset("vendor/crudbooster/assets/codemirror/mode/php/php.js")?>"></script>
    <script src="<?php echo asset("vendor/crudbooster/assets/codemirror/keymap/sublime.js")?>"></script>
    <script type="text/javascript">
        var currentInputCallback = null;
        var hookQueryIndex = null;
        var hookRowIndexEditor = null;
        var hookBeforeAdd = null;
        var hookAfterAdd = null;
        var hookBeforeEdit = null;
        var hookAfterEdit = null;
        var hookBeforeDelete = null;
        var hookAfterDelete = null;   
        var textareaCallback = null;      

        $(function() {

            hookQueryIndexEditor = CodeMirror.fromTextArea(document.getElementById('textarea-hookqueryindex'), {
                lineNumbers: true,
                matchBrackets: true,
                mode: "text/x-php",
                indentUnit: 4,
                indentWithTabs: true,
                theme:'blackboard',
                keyMap:"sublime"
            });
            hookQueryIndexEditor.setSize(null, 250);

            hookRowIndexEditor = CodeMirror.fromTextArea(document.getElementById('textarea-hookrowindex'), {
                lineNumbers: true,
                matchBrackets: true,
                mode: "text/x-php",
                indentUnit: 4,
                indentWithTabs: true,
                theme:'blackboard',
                keyMap:"sublime"
            });
            hookRowIndexEditor.setSize(null, 250);

            hookBeforeAdd = CodeMirror.fromTextArea(document.getElementById('textarea-hookBeforeAdd'), {
                lineNumbers: true,
                matchBrackets: true,
                mode: "text/x-php",
                indentUnit: 4,
                indentWithTabs: true,
                theme:'blackboard',
                keyMap:"sublime"
            });
            hookBeforeAdd.setSize(null, 250);

            hookAfterAdd = CodeMirror.fromTextArea(document.getElementById('textarea-hookAfterAdd'), {
                lineNumbers: true,
                matchBrackets: true,
                mode: "text/x-php",
                indentUnit: 4,
                indentWithTabs: true,
                theme:'blackboard',
                keyMap:"sublime"
            });
            hookAfterAdd.setSize(null, 250);

            hookBeforeEdit = CodeMirror.fromTextArea(document.getElementById('textarea-hookBeforeEdit'), {
                lineNumbers: true,
                matchBrackets: true,
                mode: "text/x-php",
                indentUnit: 4,
                indentWithTabs: true,
                theme:'blackboard',
                keyMap:"sublime"
            });
            hookBeforeEdit.setSize(null, 250);

            hookAfterEdit = CodeMirror.fromTextArea(document.getElementById('textarea-hookAfterEdit'), {
                lineNumbers: true,
                matchBrackets: true,
                mode: "text/x-php",
                indentUnit: 4,
                indentWithTabs: true,
                theme:'blackboard',
                keyMap:"sublime"
            });
            hookAfterEdit.setSize(null, 250);

            hookBeforeDelete = CodeMirror.fromTextArea(document.getElementById('textarea-hookBeforeDelete'), {
                lineNumbers: true,
                matchBrackets: true,
                mode: "text/x-php",
                indentUnit: 4,
                indentWithTabs: true,
                theme:'blackboard',
                keyMap:"sublime"
            });
            hookBeforeDelete.setSize(null, 250);

            hookAfterDelete = CodeMirror.fromTextArea(document.getElementById('textarea-hookAfterDelete'), {
                lineNumbers: true,
                matchBrackets: true,
                mode: "text/x-php",
                indentUnit: 4,
                indentWithTabs: true,
                theme:'blackboard',
                keyMap:"sublime"
            }); 
            hookAfterDelete.setSize(null, 250);

            textareaCallback = CodeMirror.fromTextArea(document.getElementById('textareaCallback'), {                    
                mode: "text/x-php",
                lineNumbers: true,                    
                theme:'blackboard',
                keyMap:"sublime"
            }); 
            textareaCallback.setSize(null, 100);
            
                    
            $(document).on('click','.btn-callback',function() {                
                var callbackValue = $(this).parent().find('input').val();  
                currentInputCallback = $(this).parent().find('input');                              
                $('#modal-callback').modal('show');     
                $('#modal-callback').on('shown.bs.modal',function(e) {
                    textareaCallback.setValue(callbackValue);
                    textareaCallback.refresh();    
                })           
            })
        })

        function saveModalCallback() {
            currentInputCallback.val(textareaCallback.getValue());
            textareaCallback.setValue('');
            $('#modal-callback').modal('hide');
        }
    </script>
    <div id="modal-callback" class="modal fade" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Edit Callback</h4>
          </div>
          <div class="modal-body">
            <textarea id="textareaCallback" class="form-control"></textarea>
            <p>                
                <div class="help-block">Must return a value <code>E.g: return number_format($row->price);</code></div>
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" onclick="saveModalCallback()" class="btn btn-primary btn-save">Save changes</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endpush
@push('head')
    <link rel="stylesheet" type="text/css" href="{{asset('vendor/crudbooster/assets/codemirror/lib/codemirror.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('vendor/crudbooster/assets/codemirror/theme/blackboard.css')}}">
@endpush

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">Hook Query Index <small>hookQueryIndex(&$query)</small></h3>
        <div class="box-tools">            
            <button class="btn btn-success btn-sm" type="submit"><i class="fa fa-save"></i> Save Module</button>
        </div>
    </div>
    <div class="box-body">
        <div class="alert alert-info"><i class="fa fa-info"></i> You can use $query to extend Laravel Database <a href='https://laravel.com/docs/queries' target='_blank'>Query Builder</a>. E.g: <code>$query->where('status','active');</code></div>
        <textarea id='textarea-hookqueryindex' class="form-control" name="hookQueryIndex">{{$hookQueryIndex}}</textarea>        
    </div>
</div>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">Hook Row Index <small>hookRowIndex($columnIndex,&$columnValue)</small></h3>
        <div class="box-tools">            
            <button class="btn btn-success btn-sm" type="submit"><i class="fa fa-save"></i> Save Module</button>
        </div>
    </div>
    <div class="box-body">
        <div class="alert alert-info"><i class="fa fa-info"></i> You can override the column value. <code>$columnIndex</code> is for column number start from 0. <code>$columnValue</code> is value that you can override</div>
        <textarea id='textarea-hookrowindex' class="form-control" name="hookRowIndex">{{$hookRowIndex}}</textarea>        
    </div>
</div>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">Hook Before Add <small>hookBeforeAdd(&$postdata)</small></h3>
        <div class="box-tools">            
            <button class="btn btn-success btn-sm" type="submit"><i class="fa fa-save"></i> Save Module</button>
        </div>
    </div>
    <div class="box-body">
        <div class="alert alert-info"><i class="fa fa-info"></i> You can override the post data before add</div>
        <textarea id='textarea-hookBeforeAdd' class="form-control" name="hookBeforeAdd">{{$hookBeforeAdd}}</textarea>        
    </div>
</div>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">Hook After Add <small>hookAfterAdd($id)</small></h3>
        <div class="box-tools">            
            <button class="btn btn-success btn-sm" type="submit"><i class="fa fa-save"></i> Save Module</button>
        </div>
    </div>
    <div class="box-body">        
        <textarea id='textarea-hookAfterAdd' class="form-control" name="hookAfterAdd">{{$hookAfterAdd}}</textarea>        
    </div>
</div>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">Hook Before Edit <small>hookBeforeEdit(&$postdata,$id)</small></h3>
        <div class="box-tools">            
            <button class="btn btn-success btn-sm" type="submit"><i class="fa fa-save"></i> Save Module</button>
        </div>
    </div>
    <div class="box-body">
        <div class="alert alert-info"><i class="fa fa-info"></i> You can override the post data before edit</div>
        <textarea id='textarea-hookBeforeEdit' class="form-control" name="hookBeforeEdit">{{$hookBeforeEdit}}</textarea>        
    </div>
</div>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">Hook After Edit <small>hookAfterEdit($id)</small></h3>
        <div class="box-tools">            
            <button class="btn btn-success btn-sm" type="submit"><i class="fa fa-save"></i> Save Module</button>
        </div>
    </div>
    <div class="box-body">        
        <textarea id='textarea-hookAfterEdit' class="form-control" name="hookAfterEdit">{{$hookAfterEdit}}</textarea>        
    </div>
</div>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">Hook Before Delete <small>hookBeforeDelete($id)</small></h3>
        <div class="box-tools">            
            <button class="btn btn-success btn-sm" type="submit"><i class="fa fa-save"></i> Save Module</button>
        </div>
    </div>
    <div class="box-body">        
        <textarea id='textarea-hookBeforeDelete' class="form-control" name="hookBeforeDelete">{{$hookBeforeDelete}}</textarea>        
    </div>
</div>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">Hook After Delete <small>hookAfterDelete($id)</small></h3>
        <div class="box-tools">            
            <button class="btn btn-success btn-sm" type="submit"><i class="fa fa-save"></i> Save Module</button>
        </div>
    </div>
    <div class="box-body">        
        <textarea id='textarea-hookAfterDelete' class="form-control" name="hookAfterDelete">{{$hookAfterDelete}}</textarea>        
    </div>
</div>

</form>
@endsection