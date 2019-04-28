@extends('crudbooster::layouts.layout')

@section('content')

    @if(!is_null($before_index_table) && !empty($before_index_table))
        {!! $before_index_table !!}
    @endif

    <div class="box">
        <div class="box-header">

            <div class="box-tools pull-{{ trans('crudbooster.right') }}" style="position: relative;margin-top: -5px;margin-right: -10px">

                @if($button_filter)
                    <a style="margin-top:-23px" href="javascript:void(0)" id='btn_advanced_filter'
                       title='{{trans('crudbooster.filter_dialog_title')}}' class="btn btn-sm btn-default">
                        <i class="fa fa-filter"></i> {{trans("crudbooster.button_filter")}}
                    </a>
                @endif

                <form method='get' style="display:inline-block;width: 260px;" action='{{ request()->url() }}'>
                    <div class="input-group">
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control input-sm pull-{{ trans('crudbooster.right') }}"
                               placeholder="{{trans('crudbooster.filter_search')}}"/>
                        {!! cb()->getUrlParameters(['q']) !!}
                        {!! csrf_field() !!}
                        <div class="input-group-btn">
                            <button type='submit' class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>


                <form method='get' id='form-limit-paging' style="display:inline-block" action='{{ request()->url() }}'>
                    {!! cb()->getUrlParameters(['limit']) !!}
                    {!! csrf_field() !!}
                    <div class="input-group">
                        <select onchange="$('#form-limit-paging').submit()" name='limit' style="width: 56px;" class='form-control input-sm'>
                            <option {{($limit==10)?'selected':''}} value='10'>10</option>
                            <option {{($limit==20)?'selected':''}} value='20'>20</option>
                            <option {{($limit==25)?'selected':''}} value='25'>25</option>
                            <option {{($limit==50)?'selected':''}} value='50'>50</option>
                            <option {{($limit==100)?'selected':''}} value='100'>100</option>
                            <option {{($limit==200)?'selected':''}} value='200'>200</option>
                        </select>
                    </div>
                </form>

            </div>

            <br style="clear:both"/>

        </div>
        <div class="box-body table-responsive no-padding">
            @include("crudbooster::module.index.table")
        </div>
    </div>

    @if(!is_null($after_index_table) && !empty($after_index_table))
        {!! $after_index_table !!}
    @endif

@endsection
