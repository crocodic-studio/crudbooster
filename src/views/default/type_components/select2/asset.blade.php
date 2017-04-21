@push('css')

	<link rel="stylesheet" href="{{ asset('vendor/crudbooster/assets/select2/dist/css/select2.min.css')}} ">
	
	<style type="text/css">
		.select2-container--default .select2-selection--single {border-radius: 0 !important}
        .select2-container .select2-selection--single {height: 35px}
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
			background-color: #3c8dbc !important;
			border-color: #367fa9 !important;
			color: #fff !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
			color: #fff !important;
        }
	</style>
	
@endpush

@push('javascript')

	<script src="{{asset('vendor/crudbooster/assets/select2/dist/js/select2.full.min.js')}}"></script>

@endpush