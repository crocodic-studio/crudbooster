@extends(getThemePath('layout.layout'))
@section('content')

    {{-- Check if there is a "ref" parameter --}}
    @if(verifyReferalUrl())
        <p>
            <a href="{{ getReferalUrl("url") }}"><i class="fa fa-arrow-left"></i> Back To {{ getReferalUrl("name")?:cbLang("data") }} List</a>
        </p>
    @endif

    {{-- This additional view is for sub module --}}
    @if(isset($additionalHeaderContent) && $additionalHeaderTitle && is_array($additionalHeaderContent))
        <div class="box box-default">
            <div class="box-header">
                <h1 class="box-title with-border">{{ $additionalHeaderTitle }}</h1>
            </div>
            <div class="box-body">
                <table class="table table-striped table-boredered">
                    <tbody>
                        @foreach($additionalHeaderContent as $label => $value)
                        <tr>
                            <th width="20%">{{ $label }}</th>
                            <td>: &nbsp; {!! $value !!}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if(isset($before_index_table))
        {!! $before_index_table !!}
    @endif

    <!-- Filter Component -->
    @include("crudbooster::module.index.filters")
    <!-- End Filter Component-->

    <div class="box">
        <div class="box-header">

            <h1 class="box-title" style="font-size: 20px;margin-top:5px">{{cbLang('browse')}} {{cbLang("data")}}</h1>

            <div class="box-tools pull-right" style="position: relative;margin-top: -5px;margin-right: -10px">

                @if(isset($search_form) && $search_form===true)
                <form method='get' style="display:inline-block;width: 260px;" action='{{ request()->url() }}'>
                    {!! cb()->getUrlParameters(['limit','page','q']) !!}
                    <div class="input-group">
                        <input type="text" name="q" value="{{ sanitizeXSS(request('q')) }}" class="form-control input-sm pull-right"
                               placeholder="{{ cbLang('search')}}"/>
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

            <div class="col-md-8">{!! $result->appends(requestAll())->links() !!}</div>

            <?php
            $from = $result->count() ? ($result->perPage() * $result->currentPage() - $result->perPage() + 1) : 0;
            $to = $result->perPage() * $result->currentPage() - $result->perPage() + $result->count();
            $total = $result->total();
            ?>
            <div class="col-md-4" style="margin:15px 0;">
                <span class="pull-right">
                    {{ cbLang("pagination_footer_total_data",[
                        "from"=>$from,
                        "to"=>$to,
                        "total"=>$total
                        ]) }}
                </span>
            </div>
        </div>
    </div>

    @if(isset($after_index_table))
        {!! $after_index_table !!}
    @endif

    @if($nowrap = getSetting("table_module_wordwrap"))
        @if($nowrap == "nowrap")
            @push("head")
            <style>
                #table-module tbody tr td {
                    white-space: nowrap;
                }
            </style>
            @endpush
        @endif
    @endif

@endsection
