@extends($ispdf?'crudbooster::admin_template_nosidebar':'crudbooster::admin_template')
@section('content') 
	
	@include($ispdf?'crudbooster::statistic_builder.pdf':'crudbooster::statistic_builder.index')

@endsection
