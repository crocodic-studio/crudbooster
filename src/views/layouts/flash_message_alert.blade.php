@if (session()->has('message'))
    <div class='alert alert-{{ session('message_type') }}'>
        <strong>{{ ucwords(session('message_type'))  }}!</strong> {!! session('message') !!}
    </div>
@endif