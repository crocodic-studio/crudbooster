<!-- Default Meta-->
<meta name="csrf-token" content="{{ csrf_token() }}"/>
<meta name='generator' content='CRUDBooster'/>
<meta name='robots' content='noindex,nofollow'/>

<!-- Default CB Stylesheet-->
<link rel='stylesheet' href='{{cbAsset("css/main.css")}}?v=1.2'/>

@if(isset($head_script))
    {!! $head_script !!}
@endif

@stack('head')

@if($style = module()->getData("style"))
    <style>
        {!! call_user_func($style) !!}
    </style>
@endif