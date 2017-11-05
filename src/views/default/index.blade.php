@extends('crudbooster::admin_template')

@section('content')


    @include("crudbooster::default._index.statistics" , ['index_statistic' => $index_statistic])


    @if(!empty($pre_index_html))
        {!! $pre_index_html !!}
    @endif


    @if(g('return_url'))
        <p>
            <a href='{{g("return_url")}}'>
                <i class='fa fa-chevron-circle-{{ cbTrans('left') }}'></i>
                &nbsp; {{cbTrans('form_back_to_list',['module'=>urldecode(g('label'))])}}
            </a>
        </p>
    @endif


    @include("crudbooster::default._index.parent_table", ['parent_table' => $parent_table])


    <div class="box">
        <div class="box-header">
            @if($button_bulk_action && ( ($button_delete && CRUDBooster::canDelete()) || $button_selected) )
                @include("crudbooster::default._index.header_btn")
            @endif
            <div class="box-tools pull-{{ cbTrans('right') }}"
                 style="position: relative;margin-top: -5px;margin-right: -10px">

                @if($button_filter)
                    <a style="margin-top:-23px" href="javascript:void(0)" id='btn_advanced_filter'
                       data-url-parameter='{{$build_query}}' title='{{cbTrans('filter_dialog_title')}}'
                       class="btn btn-sm btn-default {{(Request::get('filter_column'))?'active':''}}">
                        <i class="fa fa-filter"></i> {{cbTrans("button_filter")}}
                    </a>
                @endif

            @include("crudbooster::default._index.search", ['parameters' => Request::all()])

            @include("crudbooster::default._index.pagination_select", ['limit' => $limit ])

            </div>

            <br style="clear:both"/>

        </div>
        <div class="box-body table-responsive no-padding">
            @include("crudbooster::default.table")
        </div>
    </div>

    @if(!empty($post_index_html))
        {!! $post_index_html !!}
    @endif

@endsection
