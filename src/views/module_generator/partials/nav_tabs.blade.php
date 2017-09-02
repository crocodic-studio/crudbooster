<ul class="nav nav-tabs">

    <li role="presentation"  class="{!! $step[0]!!}">
        <a href="{{Route('AdminModulesControllerGetStep1',['id'=>$id])}}">{!! CB::icon('info') !!} Step 1 - Module Information</a>
    </li>

    <li role="presentation"  class="{!! $step[1]!!}">
        <a href="{{Route('AdminModulesControllerGetStep2',['id'=>$id])}}">{!! CB::icon('table') !!} Step 2 - Table Display</a>
    </li>

    <li role="presentation"  class="{!! $step[2]!!}">
        <a href="{{Route('AdminModulesControllerGetStep3',['id'=>$id])}}">{!! CB::icon('plus-square-o') !!} Step 3 - Form Display</a>
    </li>

    <li role="presentation"  class="{!! $step[3]!!}">
        <a href="{{Route('AdminModulesControllerGetStep4',['id'=>$id])}}">{!! CB::icon('wrench') !!} Step 4 - Configuration</a>
    </li>
</ul>