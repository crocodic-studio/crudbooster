@if ($ispdf == true)
	@extends('crudbooster::admin_template_nosidebar')
	@section('content') 
		
		@include('crudbooster::statistic_builder.pdf')

	@endsection
@else 
	@extends('crudbooster::admin_template')
	@section('content') 
		
		@include('crudbooster::statistic_builder.index')

	@endsection
@endif