@if($exportBtn)
    <a href="javascript:void(0)" id='btn_export_data'
       data-url-parameter='{{$query}}'
       title='{{ cbTrans("button_export") }}'
       class="btn btn-sm btn-primary btn-export-data">
        {!! cbIcon('upload') !!} {{ cbTrans("button_export") }}
    </a>
@endif

@if($importBtn)
    <a href="{{ CRUDBooster::mainpath('import-data') }}" id='btn_import_data'
       data-url-parameter='{{$query}}'
       title='{{ cbTrans("button_import") }}'
       class="btn btn-sm btn-primary btn-import-data">
        {!! cbIcon('download') !!} {{ cbTrans("button_import") }}
    </a>
@endif