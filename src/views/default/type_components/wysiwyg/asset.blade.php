@push('css')
	<link rel="stylesheet" type="text/css" href="{{asset('vendor/crudbooster/assets/summernote/summernote.css')}}">
@endpush

@push('javascript')
	<script type="text/javascript" src="{{asset('vendor/crudbooster/assets/summernote/summernote.js')}}"></script>
	
	<script type="text/javascript">
	
		$(document).ready(function() {
			
			$('#textarea_{{$name}}').summernote({
				height: ($(window).height() - 300),
				callbacks: {
					onImageUpload: function(image) {
						uploadImage{{$name}}(image[0]);
					}
				}
			});
		
			function uploadImage{{$name}}(image) {
				
				var data = new FormData();
				data.append("userfile", image);
				
				$.ajax({
					url: '{{CRUDBooster::mainpath("upload-summernote")}}',
					cache: false,
					contentType: false,
					processData: false,
					data: data,
					type: "post",
					success: function(url) {
						var image = $('<img>').attr('src',url);
						$('#textarea_{{$name}}').summernote("insertNode", image[0]);
					},
					error: function(data) {
						console.log(data);
					}
				});
			}
		})

	</script>
	
@endpush