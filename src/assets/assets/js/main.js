// request permission on page load
		document.addEventListener('DOMContentLoaded', function () {
		  if (!Notification) {
		    alert('Desktop notifications not available in your browser. Try Chromium.'); 
		    return;
		  }

		  if (Notification.permission !== "granted")
		    Notification.requestPermission();
		});

		Number.prototype.number_format = function(n, x) {
			var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
			return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
		};

		function beep() {
			
			$("#sound_beep").remove();
			$('body').append('<audio id="sound_beep" style="display:none" autoplay>'+
  			+'<source src="'+ASSET_URL+'/vendor/crudbooster/assets/sound/bell_ring.ogg" type="audio/ogg">'
  			+'<source src="'+ASSET_URL+'/vendor/crudbooster/assets/sound/bell_ring.mp3" type="audio/mpeg">'
			+'Your browser does not support the audio element.</audio>');
		}

		function send_notification(text,url) {
			if (Notification.permission !== "granted")
			{
				console.log("Request a permission for Chrome Notification");
				Notification.requestPermission();
			}else{
				var notification = new Notification(APP_NAME+' Notification', {
				icon:'https://cdn1.iconfinder.com/data/icons/CrystalClear/32x32/actions/agt_announcements.png',
				body: text,
				'tag' : text
				});
				console.log("Send a notification");
				beep();

				notification.onclick = function () {
			      location.href = url;    
			    };
			}
		}

		$(function() {		

			jQuery.fn.outerHTML = function(s) {
			    return s
			        ? this.before(s).remove()
			        : jQuery("<p>").append(this.eq(0).clone()).html();
			};



			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			
			$('.treeview').each(function() {
				var active = $(this).find('.active').length;
				if(active) {
					$(this).addClass('active');
				}
			})			
			
			
			$('input[type=text]').first().not(".notfocus").focus();										
			
			if($(".datepicker").length > 0) {				
				$('.datepicker').daterangepicker({					
					singleDatePicker: true,
        			showDropdowns: true,
        			minDate: '1900-01-01',
					format:'YYYY-MM-DD'
				})
			}

			if($(".datetimepicker").length > 0) {
				$(".datetimepicker").daterangepicker({
					minDate: '1900-01-01',
					singleDatePicker: true, 
				    showDropdowns: true,
				    timePicker:true,
				    timePicker12Hour: false,
				    timePickerIncrement: 5,
				    timePickerSeconds: true,
				    autoApply: true,
					format:'YYYY-MM-DD HH:mm:ss'
				})
			}

			//Timepicker
		    if($(".timepicker").length > 0) {
		    	$(".timepicker").timepicker({
			      showInputs: true,
			      showSeconds: true,
			      showMeridian:false
			    });	
		    }

		});	


		var total_notification = 0;
    function loader_notification() {       

      $.get(NOTIFICATION_JSON,function(resp) {
          if(resp.total > total_notification) {
            send_notification(NOTIFICATION_NEW,NOTIFICATION_INDEX);
          }

          $('.notifications-menu #notification_count').text(resp.total);
          if(resp.total>0) {
            $('.notifications-menu #notification_count').fadeIn();            
          }else{
            $('.notifications-menu #notification_count').hide();
          }          

          $('.notifications-menu #list_notifications .menu').empty();
		  $('.notifications-menu .header').text(NOTIFICATION_YOU_HAVE +' '+resp.total+' '+ NOTIFICATION_NOTIFICATIONS);
          var htm = '';
          $.each(resp.items,function(i,obj) {
              htm += '<li><a href="'+ADMIN_PATH+'/notifications/read/'+obj.id+'?m=0"><i class="'+obj.icon+'"></i> '+obj.content+'</a></li>';
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
    });	