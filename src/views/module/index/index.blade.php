@extends('crudbooster::layouts.layout')

@section('content')

    @if(isset($before_index_table))
        {!! $before_index_table !!}
    @endif

    <div class="box">
        <div class="box-header">

            <div class="box-tools pull-{{ trans('crudbooster.right') }}" style="position: relative;margin-top: -5px;margin-right: -10px">

                @if(isset($button_filter))
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
                            <option {{(isset($limit) && $limit==10)?'selected':''}} value='10'>10</option>
                            <option {{(isset($limit) && $limit==20)?'selected':''}} value='20'>20</option>
                            <option {{(isset($limit) && $limit==25)?'selected':''}} value='25'>25</option>
                            <option {{(isset($limit) && $limit==50)?'selected':''}} value='50'>50</option>
                            <option {{(isset($limit) && $limit==100)?'selected':''}} value='100'>100</option>
                            <option {{(isset($limit) && $limit==200)?'selected':''}} value='200'>200</option>
                        </select>
                    </div>
                </form>

            </div>

            <br style="clear:both"/>

        </div>
        <div class="box-body table-responsive">
            @include("crudbooster::module.index.table")
        </div>
    </div>

    @if(isset($after_index_table))
        {!! $after_index_table !!}
    @endif

@endsection
