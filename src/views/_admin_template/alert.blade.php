@if(@$alerts)
    @foreach(@$alerts as $alert)
        <div class='callout callout-{{$alert['type']}}'>
            {!! $alert['message'] !!}
        </div>
    @endforeach
@endif


@if (Session::get('message'))
    <div class='alert alert-{{ Session::get("message_type") }}'>
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4>
            <i class="icon fa fa-info"></i>
            {{ cbTrans("alert_".Session::get("message_type")) }}
        </h4>
        {!!Session::get('message')!!}
    </div>
@endif