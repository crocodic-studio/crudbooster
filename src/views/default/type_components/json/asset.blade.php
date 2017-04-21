@push('javascript')
	<script src="{{asset('vendor/crudbooster/assets/jsoneditor/jsoneditor.min.js')}}"></script>

	<script type="text/javascript">
		$(document).ready(function() {
			console.log(document.getElementById('{{$name}}'));
			// Set an option globally
			JSONEditor.defaults.options.theme = 'bootstrap2';
			JSONEditor.plugins.select2.enable = false;
			JSONEditor.plugins.selectize.enable = true;//to avoid select2

			// Set an option during instantiation
			var editor = new JSONEditor(document.getElementById('{{$name}}'), {
			  theme: 'bootstrap2',
			  startval : <?=json_encode(json_decode($value, false))?>,
			  schema : <?=json_encode(json_decode($form["schema"], false))?>
			});
			
			$('[name="{{$name}}"]').parents('form').on('submit', function() {
					$('[name="{{$name}}"]').val(JSON.stringify(editor.getValue()));
					return true;
			})
		});
	</script>
	
@endpush