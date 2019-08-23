@if($loginBackground = getSetting("login_background"))
    <style>
        body {
            background: #ffffff url('{{ asset($loginBackground) }}');
            @if($cover = getSetting("login_background_cover"))
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            @endif
        }
    </style>
@endif