@if($index_statistic)
    <div id='box-statistic' class='row'>
        @foreach($index_statistic as $stat)
            <div class="{{ ($stat['width'])?:'col-sm-3' }}">
                <div class="small-box bg-{{ $stat['color']?:'red' }}">
                    <div class="inner">
                        <h3>{{ $stat['count'] }}</h3>
                        <p>{{ $stat['label'] }}</p>
                    </div>
                    <div class="icon">
                        <i class="{{ $stat['icon'] }}"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif