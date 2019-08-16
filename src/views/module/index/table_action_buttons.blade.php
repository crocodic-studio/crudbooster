@if(is_array(module()->getData("add_action_button")))
@foreach(module()->getData("add_action_button") as $a)
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
        @if($a->getConfirmation())
            <a href="javascript:;" onclick="goToUrlWithConfirmation('{{$a->getUrl()}}{{ isset($subModuleKey)?"&sub_module=".$subModuleKey:null }}','{{ cbLang("do_you_want_to",["action"=>$a->getLabel()]) }}')" title="{{ $a->getLabel() }}" class="btn btn-xs btn-{{$a->getColor()}}">
                @if($a->getIcon())
                    <i class="{{ $a->getIcon() }}"></i>
                @endif
                {{ $a->getLabel() }}
            </a>
        @else
            <a href="{{ $a->getUrl() }}{{ isset($subModuleKey)?"&sub_module=".$subModuleKey:null }}" title="{{ $a->getLabel() }}" class="btn btn-xs btn-{{$a->getColor()}}">
                @if($a->getIcon())
                    <i class="{{ $a->getIcon() }}"></i>
                @endif
                {{ $a->getLabel() }}
            </a>
        @endif
    @endif
@endforeach
@endif


@if(module()->canRead() && module()->getData("button_detail"))
    @if(isset($hide_button_detail) && call_user_func($hide_button_detail, $row)===false)
    <a class='btn btn-xs btn-primary btn-detail' title='{{ cbLang("detail")." ".cbLang("data") }}'
       href='{{ module()->detailURL($row->primary_key)."?ref=".makeReferalUrl() }}{{ isset($subModuleKey)?"&sub_module=".$subModuleKey:null }}'><i class='fa fa-eye'></i></a>
    @endif
@endif

@if(module()->canUpdate() && module()->getData("button_edit"))
    @if(isset($hide_button_edit) && call_user_func($hide_button_edit, $row)===false)
    <a class='btn btn-xs btn-success btn-edit' title='{{ cbLang("edit")." ".cbLang("data") }}'
       href='{{ module()->editURL($row->primary_key)."?ref=".makeReferalUrl() }}{{ isset($subModuleKey)?"&sub_module=".$subModuleKey:null }}'><i
                class='fa fa-pencil'></i></a>
    @endif
@endif

@if(module()->canDelete() && module()->getData("button_delete"))
    @if(isset($hide_button_delete) && call_user_func($hide_button_delete, $row)===false)
    <a class='btn btn-xs btn-danger btn-delete' title='{{ cbLang("delete")." ".cbLang("data") }}' onclick="deleteConfirmation('{{ module()->deleteURL($row->primary_key)."?ref=".makeReferalUrl() }}{{ isset($subModuleKey)?"&sub_module=".$subModuleKey:null }}')" href='javascript:;'
       ><i class='fa fa-trash'></i></a>
    @endif
@endif
