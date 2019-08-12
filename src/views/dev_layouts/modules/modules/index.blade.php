@extends("crudbooster::dev_layouts.layout")
@section("content")

    <p><a href="{{ route('DeveloperModulesControllerGetAdd') }}" class="btn btn-primary"><i class="fa fa-plus"></i> {{ cbLang('add') }} Module</a></p>

    <div class="box box-default">
        <div class="box-header">
            <h1 class="box-title">Show Data</h1>
            <div class="pull-right">

            </div>
        </div>
        <div class="box-body">
            <table class="table table-bordered datatable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Controller</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($result as $row)
                        <tr>
                            <td>{{ $row->name }}</td>
                            <td>{{ $row->controller }}</td>
                            <td>
                                <a href="{{ route('DeveloperModulesControllerGetAdd') }}?rebuild={{ $row->table_name }}&modules_id={{$row->id}}" class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i> Rebuild</a>
                                <a onclick="deleteModule({{ $row->id }})" href="javascript:;" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                        @endforeach
                </tbody>
            </table>
        </div>
    </div>


    <div class="modal" id="modal-delete">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">

                    <h4 class="modal-title">Confirmation</h4>
                </div>
                <div class="modal-body">
                    <div align="center">
                        <h3>Are you sure want to delete this module?</h3>
                        <p>We can't recovery this module once you delete it. If you choose <strong>Hard Delete</strong>, the system will be deleting your controller also the module data. If you choose <strong>Soft Delete</strong>, system will be deleting the module data only</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="deleteHard()" class="btn btn-danger">Hard Delete</button>
                    <button type="button" onclick="deleteSoft()" class="btn btn-warning">Soft Delete</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Abort</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


    @push('bottom')
        <script>
            var last_module_id = null;
            function deleteModule(id) {
                $("#modal-delete").modal("show");
                last_module_id = id;
            }
            function deleteHard() {
                location.href='{{ route('DeveloperModulesControllerGetDelete') }}/'+last_module_id;
            }
            function deleteSoft() {
                location.href='{{ route('DeveloperModulesControllerGetDeleteSoft') }}/'+last_module_id;
            }
        </script>
    @endpush

@endsection