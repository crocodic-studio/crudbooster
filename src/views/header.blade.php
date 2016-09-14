<script type="text/javascript">
    var total_notification = 0;
    function loader_notification() {
      console.log("loader notifications");      

      $.get("{{route('NotificationsControllerGetLatestJson')}}",function(resp) {
          if(resp.total > total_notification) {
            send_notification('You have a new notification !',"{{route('NotificationsControllerGetIndex')}}");            
          }

          $('.notifications-menu #notification_count').text(resp.total);
          if(resp.total>0) {
            $('.notifications-menu #notification_count').fadeIn();            
          }else{
            $('.notifications-menu #notification_count').hide();
          }          

          $('.notifications-menu #list_notifications .menu').empty();
          $('.notifications-menu .header').text('You have '+resp.total+' notifications');
          var htm = '';
          $.each(resp.items,function(i,obj) {
              htm += '<li><a href="{{url(config("crudbooster.ADMIN_PATH")."/notifications/read")}}/'+obj.id+'"><i class="'+obj.icon+'"></i> '+obj.content+'</a></li>';
          })  
          $('.notifications-menu #list_notifications .menu').html(htm);

          

          total_notification = resp.total;
      })
    }
    $(function() {
      loader_notification();
      setInterval(function() {
          loader_notification();
      },10000);
    })
</script>

<!-- load js -->
    <script type="text/javascript">
      var site_url = "{{url('/')}}" ;
    </script>
    @if($load_js)
      @foreach($load_js as $js)
        <script src="{{$js}}"></script>
      @endforeach
    @endif

<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->
    <a href="{{url(config('crudbooster.ADMIN_PATH'))}}" class="logo">{{get_setting('appname')}}</a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">      

            <li class="dropdown notifications-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" title='Notifications' aria-expanded="false">
                  <i id='icon_notification' class="fa fa-bell-o"></i>
                  <span id='notification_count' class="label label-danger" style="display:none">0</span>
                </a>
                <ul id='list_notifications' class="dropdown-menu">
                  <li class="header">You have 0 notifications</li>
                  <li>
                    <!-- inner menu: contains the actual data -->
                    <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 200px;">
                    <ul class="menu" style="overflow: hidden; width: 100%; height: 200px;">
                      <li>
                        <a href="#">
                          <em>No Notifications Is Found</em>
                        </a>
                      </li>

                    </ul>
                    <div class="slimScrollBar" style="width: 3px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 7px; z-index: 99; right: 1px; height: 195.122px; background: rgb(0, 0, 0);"></div><div class="slimScrollRail" style="width: 3px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; opacity: 0.2; z-index: 90; right: 1px; background: rgb(51, 51, 51);"></div></div>
                  </li>
                  <li class="footer"><a href="{{route('NotificationsControllerGetIndex')}}">View all</a></li>
                </ul>
              </li>

                <!-- User Account Menu -->
                <li class="dropdown user user-menu">
                    <!-- Menu Toggle Button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <!-- The user image in the navbar-->
                        <img src="{{ get_my_photo() }}" class="user-image" alt="User Image"/>
                        <!-- hidden-xs hides the username on small devices so only the image appears. -->
                        <span class="hidden-xs">{{ get_my_name() }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- The user image in the menu -->
                        <li class="user-header">
                            <img src="{{ get_my_photo() }}" class="img-circle" alt="User Image" />
                            <p>
                                {{ get_my_name() }}
                                <small>{{ get_my_privilege_name() }}</small>
                                <small><em><?php echo date('d F Y')?></em> </small>
                            </p>
                        </li>

                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="{{ route('UsersControllerGetProfile') }}" class="btn btn-default btn-flat"><i class='fa fa-user'></i> Profile</a>
                            </div>
                            <div class="pull-right">
                                <a href="{{ route("getLogout") }}" class="btn btn-default btn-flat"><i class='fa fa-power-off'></i> Sign out</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>
