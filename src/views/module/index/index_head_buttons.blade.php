
@if(cb()->getCurrentMethod() == 'getIndex' && module()->getController())
    @if(module()->getData("button_add") && module()->canCreate())
        <a href="{{ module()->addURL()."?ref=".makeReferalUrl()  }}"
           id='btn_add_new_data' class="btn btn-sm btn-success" title="{{ cbLang("add").' '.cbLang('data') }}">
            <i class="fa fa-plus-circle"></i> {{ cbLang("add").' '.cbLang('data') }}
        </a>
    @endif
@endif


<!--ADD ACTION-->
@if(isset($index_action_button))
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