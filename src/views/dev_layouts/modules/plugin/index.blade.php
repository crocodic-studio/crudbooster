@extends("crudbooster::dev_layouts.layout")
@section("content")

    <div align="right">
        <a href="{{ route("DeveloperPluginStoreControllerGetIndex") }}?refresh=1" class="btn btn-success"><i class="fa fa-refresh"></i> Update List</a>
    </div>
    <div class="box box-default mt-10">
        <div class="box-body">
            <table class="table table-bordered datatable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Version</th>
                        <th>Author</th>
                        <th>Install</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($result as $row)
                        <tr>
                            <td>
                                @if($row['icon'])
                                    <i class="{{ $row['icon'] }}"></i>
                                @endif
                                {{ $row['name'] }}</td>
                            <td>{{ $row['description'] }}</td>
                            <td>{{ $row['version'] }}
                                @if(isset($row['changelog']))
                                    <a href="javascript:;" title="Changelog {{$row['version']}}:&#013;{{ $row['changelog'] }}"><i class="fa fa-question-circle"></i></a>
                                @endif
                            </td>
                            <td>
                                @if(isset($row['author_homepage']))
                                    <a target="_blank" href="{{ $row['author_homepage'] }}">{{ $row['author'] }}</a>
                                @else
                                    {{ $row['author'] }}
                                @endif
                            </td>
                            <td>
                                @if(\crocodicstudio\crudbooster\helpers\Plugin::has($row['key']))
                                    @if(\crocodicstudio\crudbooster\helpers\Plugin::isNeedUpgrade($row['key'], $row['version']))
                                        <a href="javascrip:;" onclick="installPlugin('{{ route("DeveloperPluginStoreControllerGetInstall",["key"=>$row['key']]) }}','<strong>Upgrade {{$row['name']}} plugin v{{ $row['version'] }}.</strong><br/><br/>You can upgrade it manual by run:<br/><code>composer require {{ $row['package'] }} && php artisan vendor:publish</code>')" class="btn btn-xs btn-info"><i class="fa fa-download"></i> Upgrade</a>
                                    @else
                                        <a href="javascript:;" class="btn disabled btn-xs btn-default"><i class="fa fa-check"></i> Installed</a>
                                    @endif

                                        <a href="javascript:;" onclick="uninstallPlugin('{{ route("DeveloperPluginStoreControllerGetUninstall",["key"=>$row['key']]) }}','Uninstall plugin {{$row['name']}}')" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></a>
                                @else
                                    @if($row['source'] == 'composer')
                                        <a href="javascript:;" onclick="installPlugin('{{ route("DeveloperPluginStoreControllerGetInstall",["key"=>$row['key']]) }}','<strong>Install {{$row['name']}} plugin v{{ $row['version'] }}.</strong><br/><br/>You can install it manual by run:<br/><code>composer require {{ $row['package'] }} && php artisan vendor:publish</code>')" class="btn btn-xs btn-success" title="Auto installing plugin via composer usually needs a few minutes">via Composer</a>
                                    @else
                                        <a href="javascript:;" onclick="installPlugin('{{ route("DeveloperPluginStoreControllerGetInstall",["key"=>$row['key']]) }}','Install {{$row['name']}} plugin v{{ $row['version'] }}')" class="btn btn-xs btn-success">Install</a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push("bottom")

        <div class="modal" id="modal-installation">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Plugin Installation</h4>
                    </div>
                    <div class="modal-body" style="text-align: center">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" onclick="doInstall()" class="btn btn-primary">Install Now</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <script>
            var currentPluginKey = null
            var current_install_url = null
            function doInstall() {
                showLoading("Please wait installing...")
                $.get(current_install_url,resp=>{
                    if(resp.status) {
                        swal("Success", resp.message, "success")
                    } else {
                        swal("Oops", resp.message, "warning")
                    }
                    hideLoading()
                    location.href = "{{ request()->url() }}"
                })
            }

            function installPlugin(url, message) {
                $("#modal-installation").modal("show")
                $("#modal-installation .modal-body").html( message )
                current_install_url = url
            }

            function uninstallPlugin(url, message) {
                showConfirmation("{{ cbLang("are_you_sure") }}", message, ()=>{
                    showLoading()
                    $.get(url,resp=>{
                        if(resp.status) {
                            swal("Success", resp.message, "success")
                        } else {
                            swal("Oops", resp.message, "warning")
                        }
                        hideLoading()
                        location.href = "{{ request()->url() }}"
                    })
                })
            }
        </script>
    @endpush

@endsection