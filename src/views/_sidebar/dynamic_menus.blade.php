@foreach(CRUDBooster::sidebarMenu() as $menu)
    <li data-id='{{$menu->id}}'
        class='{{(count($menu->children))?"treeview":""}} {{ (Request::is($menu->url_path."*"))?"active":""}}'>
        <a href='{{ ($menu->is_broken)?"javascript:alert('".cbTrans('controller_route_404')."')":$menu->url }}'
           class='{{($menu->color)?"text-".$menu->color:""}}'>
            <i class='{{$menu->icon}} {{($menu->color)?"text-".$menu->color:""}}'></i>
            <span>{{$menu->name}}</span>
            @if(count($menu->children)){!! CB::icon('angle-right pull-right') !!}@endif
        </a>
        @if(count($menu->children))
            <ul class="treeview-menu">
                @foreach($menu->children as $child)
                    <li data-id='{{$child->id}}'
                        class='{{(Request::is($child->url_path."*"))?"active":""}}'>
                        <a href='{{ ($child->is_broken)?"javascript:alert('".cbTrans('controller_route_404')."')":$child->url}}'
                           class='{{($child->color)?"text-".$child->color:""}}'>
                            <i class='{{$child->icon}}'></i> <span>{{$child->name}}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </li>
@endforeach