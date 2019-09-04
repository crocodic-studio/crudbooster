@extends("crudbooster::dev_layouts.layout")
@section("content")

    @push("head")
        <link rel="stylesheet" href="{{ cbAsset("js/bootstrap-toggle/bootstrap-toggle.min.css") }}">
    @endpush
    @push("bottom")
        <script src="{{ cbAsset("js/bootstrap-toggle/bootstrap-toggle.min.js") }}"></script>
        <script>
            function checkAllBrowse(t) {
                let v = $(t).prop("checked")
                $(".can-browse").bootstrapToggle((v)?"on":"off")
            }
            function checkAllCreate(t) {
                let v = $(t).prop("checked")
                $(".can-create").bootstrapToggle((v)?"on":"off")
            }
            function checkAllRead(t) {
                let v = $(t).prop("checked")
                $(".can-read").bootstrapToggle((v)?"on":"off")
            }
            function checkAllUpdate(t) {
                let v = $(t).prop("checked")
                $(".can-update").bootstrapToggle((v)?"on":"off")
            }
            function checkAllDelete(t) {
                let v = $(t).prop("checked")
                $(".can-delete").bootstrapToggle((v)?"on":"off")
            }
        </script>
    @endpush

    <div class="callout callout-info">
        <strong>Tips</strong> You can find the role data by using <code>cb()->getRoleByName("Admin")</code> helper.
    </div>

    <p>
        <a href="{{ route('DeveloperRolesControllerGetIndex') }}"><i class="fa fa-arrow-left"></i> Back To List</a>
    </p>

    <div class="box box-default">
        <div class="box-header with-border">
            <h1 class="box-title">Add Role</h1>
        </div>
        <form method="post" action="{{ route('DeveloperRolesControllerPostAddSave') }}">
            {!! csrf_field() !!}
        <div class="box-body">
            <div class="form-group">
                <label for="">Name</label>
                <input required type="text" placeholder="E.g : Admin" name="name" class="form-control">
            </div>
            <div class="form-group">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr class="active">
                            <th width="20px">No</th>
                            <th>Name</th>
                            <th width="90px">Browse <a href="#" title="Make user able to browse the data" class="fa fa-question-circle"></a></th>
                            <th width="90px">Create <a href="#" title="Make user able to create the data" class="fa fa-question-circle"></a></th>
                            <th width="90px">Read <a href="#" title="Make user able to read detail the data" class="fa fa-question-circle"></a></th>
                            <th width="90px">Update <a href="#" title="Make user able to edit or update the data" class="fa fa-question-circle"></a></th>
                            <th width="90px">Delete <a href="#" title="Make user able to delete the data" class="fa fa-question-circle"></a></th>
                        </tr>
                        <tr>
                            <th></th>
                            <th></th>
                            <th><input type="checkbox" data-toggle="toggle" onChange="checkAllBrowse(this)"></th>
                            <th><input type="checkbox" data-toggle="toggle" onChange="checkAllCreate(this)"></th>
                            <th><input type="checkbox" data-toggle="toggle" onChange="checkAllRead(this)"></th>
                            <th><input type="checkbox" data-toggle="toggle" onChange="checkAllUpdate(this)"></th>
                            <th><input type="checkbox" data-toggle="toggle" onChange="checkAllDelete(this)"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $no = 0;
                        ?>
                        @foreach($menus as $menu)
                            <tr>
                                <td>{{ ++$no }}<input type="hidden" name="menus[]" value="{{ $menu->id }}"></td>
                                <td>{{ $menu->name }}</td>
                                <td>
                                    <input type="checkbox"  class="can-browse" data-toggle="toggle" name="access[{{$menu->id}}][can_browse]" value="1">
                                </td>
                                <td><input type="checkbox"  class="can-create" data-toggle="toggle" name="access[{{$menu->id}}][can_create]" value="1"></td>
                                <td><input type="checkbox"  class="can-read" data-toggle="toggle" name="access[{{$menu->id}}][can_read]" value="1"></td>
                                <td><input type="checkbox"  class="can-update" data-toggle="toggle" name="access[{{$menu->id}}][can_update]" value="1"></td>
                                <td><input type="checkbox"  class="can-delete" data-toggle="toggle" name="access[{{$menu->id}}][can_delete]" value="1"></td>
                            </tr>
                            @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-footer">
            <input type="submit" class="btn btn-success" value="Add Role">
        </div>
        </form>
    </div>


@endsection