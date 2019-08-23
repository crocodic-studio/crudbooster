@if(isset($alert_message))
    <div class='callout callout-{{$alert_message_type}}'>
        {!! $alert_message !!}
    </div>
@endif