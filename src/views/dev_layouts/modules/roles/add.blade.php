@extends("crudbooster::dev_layouts.layout")
@section("content")


    <p>
        <a href="{{ route('DeveloperRolesControllerGetIndex') }}"><i class="fa fa-arrow-left"></i> Back To List</a>
    </p>

    <div class="box box-default">
        <div class="box-header">
            <h1 class="box-title">Add Role</h1>
        </div>
        <form method="post" action="{{ route('DeveloperRolesControllerPostAddSave') }}">
            {!! csrf_field() !!}
        <div class="box-body">
            <div class="form-group">
                <label for="">Name</label>
                <input required type="text" placeholder="E.g : Book Manager" name="name" class="form-control">
            </div>
            <div class="form-group">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr class="active">
                            <th>No</th>
                            <th>Name</th>
                            <th>Browse</th>
                            <th>Create</th>
                            <th>Read</th>
                            <th>Update</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($menus as $menu)
                            <tr>
                                <td>{{ ++$no }}</td>
                                <td>{{ $menu->name }}</td>
                                <td><select name="menus[{{$menu->id}}][can_browse]">
                                        <option value="1">YES</option>
                                        <option value="0">NO</option>
                                    </select></td>
                                <td><select name="menus[{{$menu->id}}][can_create]">
                                        <option value="1">YES</option>
                                        <option value="0">NO</option>
                                    </select></td>
                                <td><select name="menus[{{$menu->id}}][can_read]">
                                        <option value="1">YES</option>
                                        <option value="0">NO</option>
                                    </select></td>
                                <td><select name="menus[{{$menu->id}}][can_update]">
                                        <option value="1">YES</option>
                                        <option value="0">NO</option>
                                    </select></td>
                                <td><select name="menus[{{$menu->id}}][can_delete]">
                                        <option value="1">YES</option>
                                        <option value="0">NO</option>
                                    </select></td>
                            </tr>
                            @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-footer">
            <input type="submit" class="btn btn-success" value="Generate">
        </div>
        </form>
    </div>


@endsection