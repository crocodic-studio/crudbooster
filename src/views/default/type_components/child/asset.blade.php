@push('css')
	<link rel="stylesheet" href="{{ asset('vendor/crudbooster/assets/select2/dist/css/select2.min.css')}} ">

	<style>
		.select2-container--default .select2-selection--single {border-radius: 0 !important}
		.select2-container .select2-selection--single {height: 35px}
	</style>

@endpush

@push('javascript')

	<script src="{{asset('vendor/crudbooster/assets/select2/dist/js/select2.full.min.js')}}"></script>

@endpush

