@if($data)
    <table class='table table-striped table-hover table-bordered'>
        <thead>
        @include('CbPrivilege::_privileges.thead')
        </thead>
        <tbody>

        @foreach($data as $i => $module)

            <tr>
                <td>{!! $i + 1 !!}</td>
                <td>{{$module->name}}</td>
                <td class='info' align="center">
                    <input type='checkbox' title='Check All Horizontal'
                           {!!($module->privilege->can_create && $module->privilege->can_read && $module->privilege->can_edit && $module->privilege->can_delete) ? "checked" : "" !!} class='select_horizontal'/>
                </td>
                <td class='active' align="center">
                    <input type='checkbox' class='is_visible'
                           name='privileges[{{$module->id}}][is_visible]'
                           {!! $module->privilege->is_visible ? "checked" : "" !!} value='1'/>
                </td>
                <td class='warning' align="center">
                    <input type='checkbox' class='can_create'
                           name='privileges[{{$module->id}}][can_create]'
                           {!! $module->privilege->can_create ? "checked" : "" !!} value='1'/>
                </td>
                <td class='info' align="center">
                    <input type='checkbox' class='can_read'
                           name='privileges[{{$module->id}}][can_read]'
                           {!! $module->privilege->can_read ? "checked" : "" !!} value='1'/>
                </td>
                <td class='success' align="center">
                    <input type='checkbox' class='can_edit'
                           name='privileges[{{$module->id}}][can_edit]'
                           {!! $module->privilege->can_edit ? "checked" : "" !!} value='1'/>
                </td>
                <td class='danger' align="center">
                    <input type='checkbox' class='can_delete'
                           name='privileges[{{$module->id}}][can_delete]'
                           {!! $module->privilege->can_delete ? "checked" : "" !!} value='1'/>
                </td>
            </tr>

        @endforeach
        </tbody>
    </table>
@else
    <h3>
        There is no module to set permissions for.
    </h3>
@endif