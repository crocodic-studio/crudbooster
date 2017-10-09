@if($load_js)
    @foreach($load_js as $js)
        <script src="{{$js}}"></script>
    @endforeach
@endif
<script type="text/javascript">
    var site_url = "{{url('/')}}";
    @if($script_js)
        {!! $script_js !!}
    @endif
</script>