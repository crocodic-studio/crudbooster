<div class="panel-heading">
    <strong>Menu Order (Active)</strong>
    <span id='menu-saved-info' style="display:none" class='pull-right text-success'>
                        {!! CB::icon('check') !!} Menu Saved !</span>
</div>
<div class="panel-body clearfix">
    <ul class='draggable-menu draggable-menu-active'>
        @foreach($menu_active as $menu)
            <li data-id='{{$menu->id}}' data-name='{{$menu->name}}'>
                <div class='{{$menu->is_dashboard?"is-dashboard":""}}'
                     title="{{$menu->is_dashboard?'This is setted as Dashboard':''}}">
                    <i class='{{($menu->is_dashboard)?"icon-is-dashboard fa fa-dashboard":$menu->icon}}'></i> {{$menu->name}}
                    <span class='pull-right'>
                    <a class='fa fa-pencil' title='Edit'
                       href='{{route("AdminMenusControllerGetEdit",["id"=>$menu->id])}}?return_url={{urlencode(Request::fullUrl())}}'></a>&nbsp;&nbsp;
                    <a title='Delete' class='fa fa-trash' onclick='{{CRUDBooster::deleteConfirm(route("AdminMenusControllerGetDelete",["id"=>$menu->id]))}}'
                       href='javascript:void(0)'></a>
                </span>
                </div>
                <ul>
                    @if($menu->children)
                        @foreach($menu->children as $child)
                            <li data-id='{{$child->id}}' data-name='{{$child->name}}'>
                                <div class='{{$child->is_dashboard?"is-dashboard":""}}'
                                     title="{{$child->is_dashboard?'This is setted as Dashboard':''}}">
                                    <i class='{{($child->is_dashboard)?"icon-is-dashboard fa fa-dashboard":$child->icon}}'></i> {{$child->name}}
                                    <span class='pull-right'>
                                    <a class='fa fa-pencil' title='Edit'
                                       href='{{route("AdminMenusControllerGetEdit",["id"=>$child->id])}}?return_url={{urlencode(Request::fullUrl())}}'></a>
                                    &nbsp;&nbsp;<a
                                                title="Delete" class='fa fa-trash'
                                                onclick='{{CRUDBooster::deleteConfirm(route("AdminMenusControllerGetDelete",["id"=>$child->id]))}}'
                                                href='javascript:void(0)'>

                                    </a>
                                </span>
                                </div>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </li>
        @endforeach
    </ul>
    @if(count($menu_active)==0)
        <div align="center">Active menu is empty, please add new menu</div>
    @endif
</div>