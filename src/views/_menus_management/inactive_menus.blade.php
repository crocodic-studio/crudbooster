<div class="panel-heading">
    <strong>Menu Order (Inactive)</strong>
</div>
<div class="panel-body clearfix">
    <ul class='draggable-menu draggable-menu-inactive'>
        @foreach($menu_inactive as $menu)
            <li data-id='{{$menu->id}}' data-name='{{$menu->name}}'>
                <div>
                    <i class='{{$menu->icon}}'></i> {{$menu->name}}
                    <span class='pull-right'>
                        <a class='fa fa-pencil' title='Edit'
                           href='{{route("AdminMenusControllerGetEdit",["id"=>$menu->id])}}?return_url={{urlencode(Request::fullUrl())}}'></a>&nbsp;&nbsp;
                        <a title='Delete' class='fa fa-trash'
                           onclick='{{CRUDBooster::deleteConfirm(route("AdminMenusControllerGetDelete",["id"=>$menu->id]))}}'
                           href='javascript:void(0)'></a>
                    </span>
                </div>
                <ul>
                    @if($menu->children)
                        @foreach($menu->children as $child)
                            <li data-id='{{$child->id}}' data-name='{{$child->name}}'>
                                <div><i class='{{$child->icon}}'></i> {{$child->name}}
                                    <span class='pull-right'>
                                        <a class='fa fa-pencil' title='Edit'
                                           href='{{route("AdminMenusControllerGetEdit",["id"=>$child->id])}}?return_url={{urlencode(Request::fullUrl())}}'></a>&nbsp;
                                        &nbsp;
                                        <a title="Delete" class='fa fa-trash'
                                           onclick='{{CRUDBooster::deleteConfirm(route("AdminMenusControllerGetDelete",["id"=>$child->id]))}}'
                                           href='javascript:void(0)'></a>
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </li>
        @endforeach
    </ul>
    @if(count($menu_inactive)==0)
        <div align="center" id='inactive_text' class='text-muted'>Inactive menu is empty</div>
    @endif
</div>
            