@foreach(\crocodicstudio\crudbooster\Modules\MenuModule\MenuRepo::sidebarMenu() as $menu)
    <li data-id='{{$menu->id}}'
        class='{{(!empty($menu->children))?"treeview":""}} {{ (Request::is($menu->url_path."*"))?"active":""}}'>
        <a href='{{ ($menu->is_broken)?"javascript:alert('".cbTrans('controller_route_404')."')":$menu->url }}'
           class='{{($menu->color)?"text-".$menu->color:""}}'>
            <i class='{{$menu->icon}} {{($menu->color)?"text-".$menu->color:""}}'></i>
            <span>{{$menu->name}}</span>
            @if(!empty($menu->children)){!! cbIcon('angle-right pull-right') !!}@endif
        </a>
        @if(!empty($menu->children))
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