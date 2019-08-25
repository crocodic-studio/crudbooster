@if(request()->is(cb()->getAdminPath()))
    <i class="fa fa-dashboard"></i> {{ cbLang("dashboard") }}
@else
    @if(module()->getPageIcon())
        <i class="{{ module()->getPageIcon() }}"></i>
    @elseif(!module()->getPageIcon() && isset($pageIcon))
        <i class="{{ $pageIcon }}"></i>
    @endif

    @if(module()->getPageTitle())
        {{ module()->getPageTitle() }}
    @elseif(!module()->getPageTitle() && isset($page_title))
        {{ $page_title }}
    @endif
@endif


@if((cb()->getCurrentMethod() == 'getIndex' || cb()->getCurrentMethod()=="getSubModule") && module()->getController())
    @if(module()->getData("button_add") && module()->canCreate())
        <a href="{{ module()->addURL()."?ref=".makeReferalUrl()  }}{{ isset($subModuleKey)?"&sub_module=".$subModuleKey:null }}"
           id='btn_add_new_data' class="btn btn-sm btn-success" title="{{ cbLang("add").' '.cbLang('data') }}">
            <i class="fa fa-plus-circle"></i> {{ cbLang("add").' '.cbLang('data') }}
        </a>
    @endif
@endif


<!--ADD ACTION-->
@if(isset($index_action_button) && cb()->getCurrentMethod()=="getIndex")
    @foreach($index_action_button as $button)
        <?php /** @var \crocodicstudio\crudbooster\models\IndexActionButtonModel $button */ ?>
        <a href='{{$button->getUrl()}}'
           id='{{slug($button->getLabel())}}'
           class='btn btn-{{$button->getColor()}} btn-sm'
                {!! $button->getAttributes() !!}
        >
            <i class='{{$button->getIcon()}}'></i> {{$button->getLabel()}}
        </a>
    @endforeach
@endif
<!-- END BUTTON -->