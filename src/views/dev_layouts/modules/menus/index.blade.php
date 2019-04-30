@extends("crudbooster::dev_layouts.layout")
@section("content")

    @push('head')
        <style type="text/css">
            body.dragging, body.dragging * {
                cursor: move !important;
            }

            .dragged {
                position: absolute;
                opacity: 0.7;
                z-index: 2000;
            }

            .draggable-menu {
                padding: 0 0 0 0;
                margin: 0 0 0 0;
            }

            .draggable-menu li ul {
                margin-top: 6px;
            }

            .draggable-menu li div {
                padding: 5px;
                border: 1px solid #cccccc;
                background: #eeeeee;
                cursor: move;
            }

            .draggable-menu li {
                list-style-type: none;
                margin-bottom: 4px;
                min-height: 35px;
            }

            .draggable-menu li.placeholder {
                position: relative;
                border: 1px dashed #b7042c;
                background: #ffffff;
                /** More li styles **/
            }

            .draggable-menu li.placeholder:before {
                position: absolute;
                /** Define arrowhead **/
            }
        </style>
    @endpush
    @push('bottom')
        <script src='{{cbAsset("js/jquery-sortable-min.js")}}'></script>
        <script>
            var sortactive = $(".draggable-menu").sortable({
                group: '.draggable-menu',
                delay: 200,
                isValidTarget: function ($item, container) {
                    var depth = 1, // Start with a depth of one (the element itself)
                        maxDepth = 3,
                        children = $item.find('ul').first().find('li');

                    // Add the amount of parents to the depth
                    depth += container.el.parents('ul').length;

                    // Increment the depth for each time a child
                    while (children.length) {
                        depth++;
                        children = children.find('ul').first().find('li');
                    }

                    return depth <= maxDepth;
                },
                onDrop: function ($item, container, _super) {

                    var data = $('.draggable-menu-active').sortable("serialize").get();
                    var jsonString = JSON.stringify(data, null, ' ');

                    $.post("{{ route('DeveloperMenusControllerPostSaveSorting') }}", {menus: jsonString, _token: "{{csrf_token()}}"}, function (resp) {
                        console.log(resp);
                    });

                    _super($item, container);
                }
            });
        </script>
    @endpush

    <p>
        <a href="{{ route('DeveloperMenusControllerGetAdd') }}" class="btn btn-primary">Add Menu</a>
    </p>

    <div class="box box-default">
        <div class="box-header">
            <h1 class="box-title">Show Data</h1>
        </div>
        <div class="box-body">
            <ul class='draggable-menu draggable-menu-active'>
                @foreach(cb()->sidebar()->all(false) as $menu)
                    <?php /** @var \crocodicstudio\crudbooster\models\SidebarModel $menu */?>
                    <li data-id="{{ $menu->getId() }}" data-name="{{ $menu->getName() }}">
                        <div>
                            <i class='{{$menu->getIcon()}}'></i>
                            <span>
                                <a href="{{ route('DeveloperMenusControllerGetEdit',['id'=>$menu->getId()]) }}" class="btn btn-xs btn-success">Edit</a>
                                <a onclick="if(!confirm('Are you sure want to delete?')) return false" href="{{ action('DeveloperMenusController@getDelete',['id'=>$menu->getId()]) }}" class="btn btn-xs btn-warning">Delete</a>
                                {{$menu->getName()}}
                            </span>
                        </div>
                        <ul>
                        @if($menu->getSub())
                           @foreach($menu->getSub() as $sub)
                                <li data-id="{{ $sub->getId() }}" data-name="{{ $sub->getName() }}">
                                    <div>
                                        <i class='{{$sub->getIcon()}}'></i>
                                        <span>
                                            <a href="{{ route('DeveloperMenusControllerGetEdit',['id'=>$sub->getId()]) }}" class="btn btn-xs btn-success">Edit</a>
                                            <a onclick="if(!confirm('Are you sure want to delete?')) return false" href="{{ action('DeveloperMenusController@getDelete',['id'=>$sub->getId()]) }}" class="btn btn-xs btn-warning">Delete</a>
                                            {{$sub->getName()}}
                                        </span>
                                    </div>
                                    <ul>
                                    @if($sub->getSub())
                                        @foreach($sub->getSub() as $sub2)
                                        <li data-id="{{ $sub2->getId() }}" data-name="{{ $sub2->getName() }}">
                                            <div>
                                                <i class='{{$sub2->getIcon()}}'></i>
                                                <span>
                                                    <a href="{{ route('DeveloperMenusControllerGetEdit',['id'=>$sub2->getId()]) }}" class="btn btn-xs btn-success">Edit</a>
                                                    <a onclick="if(!confirm('Are you sure want to delete?')) return false" href="{{ action('DeveloperMenusController@getDelete',['id'=>$sub2->getId()]) }}" class="btn btn-xs btn-warning">Delete</a>
                                                    {{$sub2->getName()}}
                                                </span>
                                            </div>
                                        </li>
                                        @endforeach
                                    @endif
                                    </ul>
                                </li>
                            @endforeach
                        @endif
                        </ul>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>


@endsection