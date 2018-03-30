<ul class="nav nav-tabs">

    <li role="presentation" class="{!! $step[0]!!}">
        <a href="{{route('AdminModulesControllerGetStep1',['id' => $id])}}">{!! cbIcon('info') !!} Step 1 - Module
            Information</a>
    </li>

    <li role="presentation" class="{!! $step[1]!!}">
        <a href="{{route('AdminModulesControllerGetStep2',['id' => $id])}}">{!! cbIcon('table') !!} Step 2 - Table
            Display</a>
    </li>

    <li role="presentation" class="{!! $step[2]!!}">
        <a href="{{route('AdminModulesControllerGetStep3',['id' => $id])}}">{!! cbIcon('plus-square-o') !!} Step 3 -
            Form Display</a>
    </li>

    <li role="presentation" class="{!! $step[3]!!}">
        <a href="{{route('AdminModulesControllerGetStep4',['id' => $id])}}">{!! cbIcon('wrench') !!} Step 4 -
            Configuration</a>
    </li>
</ul>