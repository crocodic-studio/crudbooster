@extends('crudbooster::layouts.layout')

@section('content')

    @if(isset($before_index_table))
        {!! $before_index_table !!}
    @endif

    <div class="box">
        <div class="box-header">

            <h1 class="box-title" style="font-size: 20px;margin-top:5px">Table Data</h1>

            <div class="box-tools pull-{{ trans('crudbooster.right') }}" style="position: relative;margin-top: -5px;margin-right: -10px">

                @if(isset($search_form) && $search_form===true)
                <form method='get' style="display:inline-block;width: 260px;" action='{{ request()->url() }}'>
                    <div class="input-group">
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control input-sm pull-{{ trans('crudbooster.right') }}"
                               placeholder="{{trans('crudbooster.filter_search')}}"/>
                        <div class="input-group-btn">
                            <button type='submit' class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>
                @endif

                @if(isset($button_limit_page) && $button_limit_page===true)
                <form method='get' id='form-limit-paging' style="display:inline-block" action='{{ request()->url() }}'>
                    {!! cb()->getUrlParameters(['limit']) !!}
                    <div class="input-group">
                        <select onchange="$('#form-limit-paging').submit()" name='limit' style="width: 56px;" class='form-control input-sm'>
                            <option {{(request('limit') && request('limit')==10)?'selected':''}} value='10'>10</option>
                            <option {{(request('limit') && request('limit')==20)?'selected':''}} value='20'>20</option>
                            <option {{(request('limit') && request('limit')==25)?'selected':''}} value='25'>25</option>
                            <option {{(request('limit') && request('limit')==50)?'selected':''}} value='50'>50</option>
                            <option {{(request('limit') && request('limit')==100)?'selected':''}} value='100'>100</option>
                            <option {{(request('limit') && request('limit')==200)?'selected':''}} value='200'>200</option>
                        </select>
                    </div>
                </form>
                @endif

            </div>

            <br style="clear:both"/>

        </div>
        <div class="box-body table-responsive">
            @include("crudbooster::module.index.table")

            <div class="col-md-8">{!! $result->appends(request()->all())->links() !!}</div>

            <?php
            $from = $result->count() ? ($result->perPage() * $result->currentPage() - $result->perPage() + 1) : 0;
            $to = $result->perPage() * $result->currentPage() - $result->perPage() + $result->count();
            $total = $result->total();
            ?>
            <div class="col-md-4" style="margin:30px 0;">
                <span class="pull-right">{{ trans("crudbooster.filter_rows_total") }}
                : {{ $from }} {{ trans("crudbooster.filter_rows_to") }} {{ $to }} {{ trans("crudbooster.filter_rows_of") }} {{ $total }}</span>
            </div>
        </div>
    </div>

    @if(isset($after_index_table))
        {!! $after_index_table !!}
    @endif

@endsection
