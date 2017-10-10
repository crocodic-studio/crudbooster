@if($exportBtn && CRUDBooster::getCurrentMethod() == 'getIndex')
    <a href="javascript:void(0)" id='btn_export_data' data-url-parameter='{{$query}}'
       title='Export Data' class="btn btn-sm btn-primary btn-export-data">

        {!!  CB::icon('upload') !!}
        {{cbTrans("button_export")}}
    </a>
@endif

@if($importBtn && CRUDBooster::getCurrentMethod() == 'getIndex')
    <a href="{{ CRUDBooster::mainpath('import-data') }}" id='btn_import_data'
       data-url-parameter='{{$query}}' title='Import Data'
       class="btn btn-sm btn-primary btn-import-data">

        {!!  CB::icon('download') !!}
        {{cbTrans("button_import")}}
    </a>
@endif