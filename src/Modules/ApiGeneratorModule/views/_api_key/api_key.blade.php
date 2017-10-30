<tr>
    <td>{{ $no + 1 }}</td>
    <td>{{ $row->screetkey }}</td>
    <td>{{ $row->hit }}</td>
    <td>{!! ($row->status=='active')?"<span class='label label-success'>Active</span>":"<span class='label label-default'>Non Active</span>" !!}</td>
    <td>
        <a class='btn btn-xs btn-default' href='
        @if($row->status == 'active')
        {{ CRUDBooster::mainpath("status-apikey?id={$row->id}&status=0") }}'>
            Non Active
            @else
                {{ CRUDBooster::mainpath("status-apikey?id={$row->id}&status=1") }}'>
                Active
            @endif
        </a>

        <a class='btn btn-xs btn-danger' href='javascript:void(0)' onclick='deleteApi({{$row->id}})'>
            Delete
        </a>
    </td>
</tr>