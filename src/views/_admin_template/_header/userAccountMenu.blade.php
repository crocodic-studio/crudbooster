<li class="dropdown user user-menu">
    <!-- Menu Toggle Button -->
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <!-- The user image in the navbar-->
        <img src="{{ cbUser()->myPhoto() }}" class="user-image" alt="User Image"/>
        <!-- hidden-xs hides the username on small devices so only the image appears. -->
        <span class="hidden-xs">{{ cbUser()->name }}</span>
    </a>
    <ul class="dropdown-menu">
        <!-- The user image in the menu -->
        <li class="user-header">
            <img src="{{ cbUser()->myPhoto() }}" class="img-circle" alt="User Image"/>
            <p>
                {{ cbUser()->name }}
                <small>{{ cbUser()->role()->name }}</small>
                <small><em>{!! date('d F Y') !!} </em></small>
            </p>
        </li>

        <!-- Menu Footer-->
        <li class="user-footer">
            <div class="pull-{{ cbTrans('left') }}">
                <a href="{{ CB::adminPath('users/profile') }}" class="btn btn-default btn-flat">
                    <i class='fa fa-user'></i> {{ cbTrans("label_button_profile")}}</a>
            </div>
            <div class="pull-{{ cbTrans('right') }}">
                <a title='Lock Screen' href="{{ route('getLockScreen') }}"
                   class='btn btn-default btn-flat'>{!! cbIcon('key') !!}</a>
                <a href="javascript:void(0)" onclick="swal({
                        title: '{{ cbTrans('alert_want_to_logout')}}',
                        type:'info',
                        showCancelButton:true,
                        allowOutsideClick:true,
                        confirmButtonColor: '#DD6B55',
                        confirmButtonText: '{{ cbTrans('button_logout')}}',
                        cancelButtonText: '{{ cbTrans('button_cancel')}}',
                        closeOnConfirm: false
                        }, function(){
                        location.href = '{{ route("getLogout") }}';

                        });" title="{{ cbTrans('button_logout')}}" class="btn btn-danger btn-flat">
                    {!! cbIcon('power-off') !!}
                </a>
            </div>
        </li>

    </ul>
</li>