@extends("crudbooster::dev_layouts.layout")
@section("content")


    <p>
        <a href="{{ route('DeveloperModulesControllerGetIndex') }}"><i class="fa fa-arrow-left"></i> Back To List</a>
    </p>

    <div class="box box-default">
        <div class="box-header">
            <h1 class="box-title">Add Module</h1>
        </div>
        <form method="post" action="{{ route('DeveloperModulesControllerPostAddSave') }}">
            {!! csrf_field() !!}
        <div class="box-body">
            <div class="form-group">
                <label for="">Name</label>
                <input required type="text" placeholder="E.g : Book Manager" name="name" class="form-control">
            </div>
            <div class="form-group">
                <label for="">Icon</label>
                <input required type="text" placeholder="E.g : fa fa-bars" name="icon" value="fa fa-bars" class="form-control">
            </div>
            <div class="form-group">
                <label for="">From Table</label>
                <select required name="table" class="form-control">
                    @foreach($tables as $table)
                        <option value="{{ $table }}">{{ $table }}</option>
                        @endforeach
                </select>
            </div>
        </div>
        <div class="box-footer">
            <input type="submit" class="btn btn-success" value="Generate">
        </div>
        </form>
    </div>


@endsection