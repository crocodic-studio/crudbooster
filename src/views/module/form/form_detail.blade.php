@extends(getThemePath('layout.layout'))
@section('content')
    @push('head')
        <style type="text/css">
            #table-detail tr td:first-child {
                font-weight: bold;
                width: 25%;
            }
        </style>
    @endpush

    @if(verifyReferalUrl())
        <p>
            <a href="{{ getReferalUrl("url") }}"><i class="fa fa-arrow-left"></i> Back To {{ getReferalUrl("name")?:cbLang("data") }} List</a>
        </p>
    @else
        <p>
            <a href="{{ module()->url() }}"><i class="fa fa-arrow-left"></i> &nbsp; {{cbLang('back_to_list')}}</a>
        </p>
    @endif



    @if(isset($before_detail_form))
        @if(is_callable($before_detail_form))
            {!! call_user_func($before_detail_form, $row) !!}
        @elseif(is_string($before_detail_form))
            {!! $before_detail_form !!}
        @endif
     @endif


    <?php
    /** @var $row object */
    columnSingleton()->valueAssignment($row);
    $detailColumns = module()->getColumnSingleton()->getDetailColumns();
    ?>
    @if($detailColumns)
    <div class="box box-default">
        <div class="box-header with-border">
            <h1 class="box-title"><i class="fa fa-eye"></i> {{ cbLang("detail") }}</h1>
        </div>
        <div class="box-body">
            <div class='table-responsive'>
                @include("crudbooster::module.form.form_detail_table")
            </div>
        </div>
    </div>
    @endif

    @if(isset($after_detail_form))
        @if(is_callable($after_detail_form))
            {!! call_user_func($after_detail_form, $row) !!}
        @elseif(is_string($after_detail_form))
            {!! $after_detail_form !!}
        @endif
    @endif
@endsection