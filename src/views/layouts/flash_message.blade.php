@if (session()->has('message'))
    <div class='callout callout-{{ session('message_type') }}'>
        <strong>{{ ucwords(session('message_type'))  }}!</strong> {!! session('message') !!}
    </div>
@endif