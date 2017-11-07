<form method='get' style="display:inline-block;width: 260px;" action='{{Request::url()}}'>
    <div class="input-group">
        <input type="text" name="q" value="{{ Request::get('q') }}"
               class="form-control input-sm pull-{{ cbTrans('right') }}"
               placeholder="{{cbTrans('filter_search')}}"/>
        {!! CRUDBooster::getUrlParameters(['q']) !!}
        <div class="input-group-btn">
            @if(Request::get('q'))
                <?php
                unset($parameters['q']);
                $build_query = urldecode(http_build_query($parameters));
                $build_query = ($build_query) ? "?".$build_query : "";
                $build_query = (Request::all()) ? $build_query : "";
                ?>
                <button type='button'
                        onclick='location.href="{{ CRUDBooster::mainpath().$build_query}}"'
                        title="{{cbTrans('button_reset')}}" class='btn btn-sm btn-warning'>
                    <i class='fa fa-ban'></i></button>
            @endif
            <button type='submit' class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
        </div>
    </div>
</form>