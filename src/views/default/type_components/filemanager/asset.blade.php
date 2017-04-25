@push('javascript')
	<script src="{{asset('vendor/laravel-filemanager/js/lfm.js')}}"></script>

	@if(@$form['filemanager_type'])
		<script type="text/javascript">$('#lfm-{{$name}}').filemanager('file','{{url("/")}}');</script>
	@else
		<script type="text/javascript">$('#lfm-{{$name}}').filemanager('images','{{url("/")}}');</script>
	@endif

@endpush

