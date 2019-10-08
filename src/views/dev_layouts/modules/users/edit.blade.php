@extends("crudbooster::dev_layouts.layout")
@section("content")


    <p>
        <a href="{{ route('DeveloperUsersControllerGetIndex') }}"><i class="fa fa-arrow-left"></i> Back To List</a>
    </p>

    <div class="box box-default">
        <div class="box-header">
            <h1 class="box-title">Edit User</h1>
        </div>
        <form method="post" action="{{ cb()->getDeveloperUrl("users/edit-save/".$row->id) }}">
            {!! csrf_field() !!}
        <div class="box-body">
            <div class="form-group">
                <label for="">Name</label>
                <input required type="text" value="{{ $row->name }}" placeholder="E.g : John Doe" name="name" class="form-control">
            </div>
            <div class="form-group">
                <label for="">Role</label>
                <select name="cb_roles_id" required class="form-control">
                    <option value="">** Select a Role</option>
                    @foreach($roles as $role)
                        <option {{ ($row->cb_roles_id==$role->id)?"selected":"" }} value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="">E-mail</label>
                <input required type="email" value="{{ $row->email }}" placeholder="E.g : john@email.com" name="email" class="form-control">
            </div>
            <div class="form-group">
                <label for="">Password</label>
                <input type="password" placeholder="Leave it blank if not changed" name="password" class="form-control">
            </div>
        </div>
        <div class="box-footer">
            <input type="submit" class="btn btn-success" value="Save User">
        </div>
        </form>
    </div>


@endsection