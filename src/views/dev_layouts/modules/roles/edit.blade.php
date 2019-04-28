@extends("crudbooster::dev_layouts.layout")
@section("content")


    <p>
        <a href="{{ action('DeveloperRolesController@getIndex') }}"><i class="fa fa-arrow-left"></i> Back To List</a>
    </p>

    <div class="box box-default">
        <div class="box-header">
            <h1 class="box-title">Edit Role</h1>
        </div>
        <form method="post" action="{{ action('DeveloperRolesController@postEditSave',['id'=>$row->id]) }}">
            {!! csrf_field() !!}
        <div class="box-body">
            <div class="form-group">
                <label for="">Name</label>
                <input required type="text" value="{{ $row->name }}" placeholder="E.g : Book Manager" name="name" class="form-control">
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
                                        <option value="0">NO</option>
                                        <option {{ $menu->can_browse?"selected":"" }} value="1">YES</option>
                                    </select></td>
                                <td><select name="menus[{{$menu->id}}][can_create]">
                                        <option value="0">NO</option>
                                        <option {{ $menu->can_create?"selected":"" }} value="1">YES</option>
                                    </select></td>
                                <td><select name="menus[{{$menu->id}}][can_read]">
                                        <option value="0">NO</option>
                                        <option {{ $menu->can_read?"selected":"" }} value="1">YES</option>
                                    </select></td>
                                <td><select name="menus[{{$menu->id}}][can_update]">
                                        <option value="0">NO</option>
                                        <option {{ $menu->can_update?"selected":"" }} value="1">YES</option>
                                    </select></td>
                                <td><select name="menus[{{$menu->id}}][can_delete]">
                                        <option value="0">NO</option>
                                        <option {{ $menu->can_delete?"selected":"" }} value="1">YES</option>
                                    </select></td>
                            </tr>
                            @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-footer">
            <input type="submit" class="btn btn-success" value="Save Role">
        </div>
        </form>
    </div>


@endsection