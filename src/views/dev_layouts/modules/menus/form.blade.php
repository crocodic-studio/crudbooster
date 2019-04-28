@extends("crudbooster::dev_layouts.layout")
@section("content")


    <p>
        <a href="{{ action('DeveloperMenusController@getIndex') }}"><i class="fa fa-arrow-left"></i> Back To List</a>
    </p>

    <div class="box box-default">
        <div class="box-header">
            <h1 class="box-title">{{ $form_title }}</h1>
        </div>
        <form method="post" action="{{ $form_url }}}">
            {!! csrf_field() !!}
        <div class="box-body">
            <div class="form-group">
                <label for="">Name</label>
                <input required type="text" placeholder="Type menu name here" value="{{ @$row->name }}" name="name" class="form-control">
            </div>

            <div class="form-group">
                <label for="">Icon</label>
                <input required type="text" placeholder="Fontawesome, e.g : fa fa-bars" value="{{ @$row->icon }}" name="icon" class="form-control">
            </div>

            <div class="form-group">
                <label for="">Type</label>
                <select required name="type" id="type" class="form-control">
                    <option {{ ($row && $row->type=="module")?"selected":"" }} value="module">Module</option>
                    <option {{ ($row && $row->type=="url")?"selected":"" }} value="url">URL</option>
                    <option {{ ($row && $row->type=="path")?"selected":"" }} value="path">Path (Prefix Admin URL)</option>
                </select>
            </div>

            <div class="form-group" id="module-input-wrap">
                <label for="">Module</label>
                <select required name="cb_modules_id" id="cb_modules_id" class="form-control">
                    @foreach($modules as $module)
                        <option {{ ($row && $row->cb_modules_id==$module->id)?"selected":"" }} value="{{ $module->id }}">{{ $module->name }}</option>
                    @endforeach
                </select>
            </div>

        </div>
        <div class="box-footer">
            <input type="submit" class="btn btn-success" value="Save Menu">
        </div>
        </form>
    </div>


@endsection