@extends('crudbooster::admin_template')
@section('content')

    <div class="row">
        <div class="col-sm-3"></div>
        <div class="col-sm-6">
            <div class="panel panel-default">
                <div class="panel-heading">{{ trans('crudbooster.edit_data_page_title',['module'=>'Data']) }}</div>
                <form method="post" action="{{ cb()->adminPath('profile/save') }}" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                <div class="panel-body">
                    <div class="form-group">
                        <label for="">Name</label>
                        <input type="text" name="name" class="form-control" required value="{{ cb()->auth()->name() }}">
                    </div>
                    <div class="form-group">
                        <label for="">Photo</label>
                        @if(cb()->auth()->user()->photo)
                            <p class="text-center"><img src="{{ asset(cb()->auth()->user()->photo) }}" width="150px" alt="Photo"></p>
                        @endif
                        <input type="file" name="photo" accept="image/jpeg,image/png,image/jpg" class="form-control">
                        <div class="help-block">{{ trans('crudbooster.supported_only_image') }}</div>
                    </div>
                    <div class="form-group">
                        <label for="">{{ config('crudbooster.LOGIN_ID_COLUMN.label','Email') }}</label>
                        <input type="text" name="{{ config('crudbooster.LOGIN_ID_COLUMN.column','email') }}" class="form-control" required value="{{ cb()->auth()->user()->{config('crudbooster.LOGIN_ID_COLUMN.column','email')} }}">
                    </div>
                    <div class="form-group">
                        <label for="">{{ config('crudbooster.LOGIN_PASS_COLUMN.label','Password') }}</label>
                        <input type="password" name="{{ config('crudbooster.LOGIN_PASS_COLUMN.column','password') }}" class="form-control" placeholder="Leave empty if not changed">
                    </div>

                </div>
                <div class="panel-footer">
                    <input type="submit" class="btn btn-primary" value="{{ trans('crudbooster.button_save') }}">
                </div>
                </form>
            </div>
        </div>
        <div class="col-sm-3"></div>
    </div>
@endsection