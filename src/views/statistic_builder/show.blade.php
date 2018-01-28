@extends($ispdf?'crudbooster::admin_template_nosidebar':'crudbooster::statistic_builder.index')
@section('content') 
	
	@include($ispdf?'crudbooster::statistic_builder.pdf':'crudbooster::statistic_builder.index')

@endsection
