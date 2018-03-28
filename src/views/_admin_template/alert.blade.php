@if(@$alerts)
    @foreach(@$alerts as $alert)
        <div class='callout callout-{{$alert['type']}}'>
            {!! $alert['message'] !!}
        </div>
    @endforeach
@endif


@if (session('message'))
    <div class='alert alert-{{ session("message_type") }}'>
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4>
            <i class="icon fa fa-info"></i>
            {{ cbTrans("alert_".session("message_type")) }}
        </h4>
        {!!session('message')!!}
    </div>
@endif