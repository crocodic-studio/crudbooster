@extends("crudbooster::dev_layouts.layout")
@section("content")

    <div class="box box-default">

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
                            <td>{{ $row['version'] }}</td>
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
                                        <a href="javascrip:;" onclick="goToUrlWithConfirmation('{{ route("DeveloperPluginStoreControllerGetInstall",["key"=>$row['key']]) }}','Upgrade {{$row['name']}} plugin to {{ $row['version'] }} version')" class="btn btn-xs btn-info"><i class="fa fa-download"></i> Upgrade</a>
                                    @else
                                        <a href="javascript:;" class="btn disabled btn-xs btn-default"><i class="fa fa-check"></i> Latest</a>
                                    @endif

                                        <a href="javascript:;" onclick="goToUrlWithConfirmation('#','Uninstall plugin {{$row['name']}}')" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Uninstall</a>
                                @else
                                    <a href="javascript:;" onclick="goToUrlWithConfirmation('{{ route("DeveloperPluginStoreControllerGetInstall",["key"=>$row['key']]) }}','Install {{$row['name']}} plugin v{{ $row['version'] }}')" class="btn btn-xs btn-success">Install</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>



@endsection