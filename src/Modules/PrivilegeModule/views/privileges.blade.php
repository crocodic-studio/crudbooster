@extends('crudbooster::admin_template')

@section('content')

    <div style="width:750px;margin:0 auto ">


        @if(CRUDBooster::getCurrentMethod() != 'getProfile')
            <p>
                <a href='{{CRUDBooster::mainpath()}}'>
                    {{ cbTrans("form_back_to_list", ['module'=>CRUDBooster::getCurrentModule()->name])}}
                </a>
            </p>
        @endif

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">{{ $page_title }}</h3>
                <div class="box-tools">

                </div>
            </div>
            <form method='post'
                  action='{{ ($role->id)?route("AdminPrivilegesControllerPostEditSave")."/{$role->id}":route("AdminPrivilegesControllerPostAddSave") }}'>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="box-body">

                    <div class='form-group'>
                        <label>{{ cbTrans('privileges_name')}}</label>
                        <input type='text' class='form-control' name='name' required value='{{ $role->name }}'/>
                        <div class="text-danger">{{ $errors->first('name') }}</div>
                    </div>


                    <div class='form-group'>
                        @include('CbPrivilege::_privileges.set_super_admin')
                    </div>

                    <div class='form-group'>
                        @include('CbPrivilege::_privileges.themePicker')
                    </div>


                    <div id='privileges_configuration' class='form-group'>
                        <label>{{ cbTrans('privileges_configuration') }}</label>

                        @push('bottom')
                            @include('CbPrivilege::_privileges.bottom_js')
                        @endpush

                        @widget('\crocodicstudio\crudbooster\Modules\PrivilegeModule\PrivilegeTableWidget', ['roleId' => $id])


                    </div>
                </div>

                @include('CbPrivilege::_privileges.alert')
                @include('CbPrivilege::_privileges.footer')
            </form>
        </div>
    </div>

    </div>
@endsection