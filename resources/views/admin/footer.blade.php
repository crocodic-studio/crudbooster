
<script>
	var lock_screen_timeout;
	var lock_screen_time_minutes = <?php echo $setting->app_lockscreen_timeout?:30?>;
	lock_screen_time_minutes = lock_screen_time_minutes * (60 * 1000);
	$(function() {
		$( "body" ).mousemove(function( event ) {
			clearTimeout(lock_screen_timeout);
			lock_screen_timeout = setTimeout(function() {
				location.href = "{{url('admin/lockscreen')}}";
			},lock_screen_time_minutes);
		});
	})
</script>

<footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs"> 
        Powered By {{get_setting('appname')}}
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; <?php echo date('Y') ?>. All rights reserved.</strong>
</footer>
