<div class="pull-{{ cbTrans('left') }}">
    <div class="selected-action" style="display:inline-block;position:relative;">


        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
            <i class='fa fa-check-square-o'></i> {{cbTrans("button_selected_action")}}
            <span class="fa fa-caret-down"></span>
        </button>


        <ul class="dropdown-menu">
            @if($button_delete && CRUDBooster::canDelete())
                <li>
                    <a href="javascript:void(0)" data-name='delete' title='{{cbTrans('action_delete_selected')}}'>
                        <i class="fa fa-trash"></i> {{cbTrans('action_delete_selected')}}
                    </a>
                </li>
            @endif

            @if($button_selected)
                @foreach($button_selected as $button)
                    <li>
                        <a href="javascript:void(0)" data-name='{{$button["name"]}}'
                           title='{{$button["label"]}}'>
                            <i class="fa fa-{{$button['icon']}}"></i> {{$button['label']}}
                        </a>
                    </li>
                @endforeach
            @endif

        </ul>
    </div>
</div>