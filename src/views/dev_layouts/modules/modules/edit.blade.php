@extends("crudbooster::dev_layouts.layout")
@section("content")


    <p>
        <a href="{{ route('DeveloperModulesControllerGetIndex') }}"><i class="fa fa-arrow-left"></i> Back To List</a>
    </p>

    <div class="box box-default">
        <div class="box-header">
            <h1 class="box-title">Edit Module</h1>
        </div>
        <form method="post" action="{{ route('DeveloperModulesControllerPostEditSave',['id'=>$row->id]) }}">
            {!! csrf_field() !!}
        <div class="box-body">
            <div class="form-group">
                <label for="">Name</label>
                <input required type="text" placeholder="E.g : Book Manager" value="{{ $row->name }}" name="name" class="form-control">
            </div>
            <div class="form-group">
                <label for="">Icon</label>
                <input required type="text" placeholder="E.g : fa fa-bars" name="icon" value="{{ $row->icon }}" class="form-control">
            </div>
        </div>
        <div class="box-footer">
            <input type="submit" class="btn btn-success" value="Save Module">
        </div>
        </form>
    </div>


@endsection