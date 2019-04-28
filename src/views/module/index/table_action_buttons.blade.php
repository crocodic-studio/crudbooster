@foreach($add_action_button as $a)
    <?php /** @var \crocodicstudio\crudbooster\models\AddActionButtonModel $a */?>
    @php
        if(is_callable($a->getUrl())) {
            $a->setUrl(call_user_func($a->getUrl(), $row));
        }
        $isShown = true;
        if($a->getCondition() && is_callable($a->getCondition())) {
            $isShown = call_user_func($a->getCondition(), $row);
        }
    @endphp

    @if($isShown)
        <a href="{{ $a->getUrl() }}" title="{{ $a->getLabel() }}" class="btn btn-sm btn-{{$a->getColor()}} {{ $a->getConfirmation()?"confirmation":"" }}">
            @if($a->getIcon())
                <i class="{{ $a->getIcon() }}"></i>
            @endif
            {{ $a->getLabel() }}
        </a>
    @endif
@endforeach


@if(module()->canRead() && module()->getData("button_detail"))
    <a class='btn btn-xs btn-primary btn-detail' title='{{trans("crudbooster.action_detail_data")}}'
       href='{{ module()->detailURL($row->primary_key) }}'><i class='fa fa-eye'></i></a>
@endif

@if(module()->canEdit() && module()->getData("button_edit"))
    <a class='btn btn-xs btn-success btn-edit' title='{{trans("crudbooster.action_edit_data")}}'
       href='{{ module()->editURL($row->primary_key) }}'><i
                class='fa fa-pencil'></i></a>
@endif

@if(module()->canDelete() && module()->getData("button_delete"))
    <a class='btn btn-xs btn-warning btn-delete' title='{{trans("crudbooster.action_delete_data")}}' href='{{ module()->deleteURL($row->primary_key) }}'
       onclick='if(!confirm("Are you sure want to delete?")) return false'><i class='fa fa-trash'></i></a>
@endif
