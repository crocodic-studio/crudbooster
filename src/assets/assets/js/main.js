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
			console.log("Play a Sound notification");
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

		function date_time(id)
		{
				date = new Date;
				year = date.getFullYear();
				month = date.getMonth();
				months = new Array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
				d = date.getDate();
				day = date.getDay();
				days = new Array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
				h = date.getHours();
				if(h<10)
				{
						h = "0"+h;
				}
				m = date.getMinutes();
				if(m<10)
				{
						m = "0"+m;
				}
				s = date.getSeconds();
				if(s<10)
				{
						s = "0"+s;
				}
				result = ''+days[day]+', '+d+' '+months[month]+' '+year+' '+h+':'+m+':'+s;
				document.getElementById(id).innerHTML = result;
				setTimeout('date_time("'+id+'");','1000');
				return true;
		}
		function pad(pad, str, padLeft) {
		  if (typeof str === 'undefined') 
			return pad;
		  if (padLeft) {
			return (pad + str).slice(-pad.length);
		  } else {
			return (str + pad).substring(0, pad.length);
		  }
		}
		 function secondsToString(seconds)
		{
			var numyears = Math.floor(seconds / 31536000);
			var numdays = Math.floor((seconds % 31536000) / 86400); 
			var numhours = Math.floor(((seconds % 31536000) % 86400) / 3600);
			var numminutes = Math.floor((((seconds % 31536000) % 86400) % 3600) / 60);
			var numseconds = (((seconds % 31536000) % 86400) % 3600) % 60;
			numseconds = parseFloat(numseconds).toFixed(0);
			
			
			numseconds = pad('00',numseconds,true);
			numminutes = pad('00',numminutes,true);
			numhours = pad('00',numhours,true);
			
			return numhours + " jam " + numminutes + " menit " + numseconds + " detik";
		}
		
		function get_interval_minutes(date1,date2,type) {
			var today = date1;
			var nextdate = date2;
			var diffMs = Math.abs(nextdate - today); 
			var diffDays = Math.round(diffMs / 86400000); 
			var diffHrs = Math.round((diffMs % 86400000) / 3600000); 
			var diffMins = Math.round(((diffMs % 86400000) % 3600000) / 60000); 
			switch(type) {
				case "minutes":
					return diffMins;
				break;
				case "days":
					return diffDays;
				break;
				case "hours":
					return diffHrs;
				break;
			}
		}
		function show_alert_floating(message) {
			$(".alert_floating .message").text(message);
			$(".alert_floating").slideDown();						
		}
		function hide_alert_floating() {			
			$(".alert_floating").slideUp();
		}
		$(function() {		

			$('.inputMoney').priceFormat({
				prefix: '',
				centsLimit:0,
			    clearPrefix: true
			});	

			jQuery.fn.outerHTML = function(s) {
			    return s
			        ? this.before(s).remove()
			        : jQuery("<p>").append(this.eq(0).clone()).html();
			};

			$(".fancybox").fancybox();

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
			
			//iCheck for checkbox and radio inputs
			$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
			  checkboxClass: 'icheckbox_minimal-blue',
			  radioClass: 'iradio_minimal-blue'
			});
			
			$('input[type=text]').first().not(".notfocus").focus();

			if(SUB_MODULE) {
				var first_form_simple = $('div[id^=form_simple]').find('input[type=text]').first();
				setTimeout(function() {
					first_form_simple.not(".notfocus").focus();
				},0);	
				var first_form_simple_id = $('div[id^=form_simple]').attr('id');
				location.href = '#'+first_form_simple_id;			
			}							
			
			if($("#tanggal").length > 0) {
				date_time("tanggal");
			}
			
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

			$(document).on('click',".ajax-button",function() {
				$("body").css("cursor", "progress");
				var title = $(this).attr('title');
				show_alert_floating('Please wait while loading '+title+'...');
				var u = $(this).attr('href');
				$(this).addClass('disabled');
				$.get(u,function(resp) {
					$("body").css("cursor", "default");
					var htm = $(resp).find('#content_section').html();
					$("#content_section").html(htm);		
					hide_alert_floating();			
				});
				return false;
			})


		});	


		var total_notification = 0;
    function loader_notification() {
      console.log("loader notifications");      

      $.get(NOTIFICATION_JSON,function(resp) {
          if(resp.total > total_notification) {
            send_notification('You have a new notification !',NOTIFICATION_INDEX);            
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
              htm += '<li><a href="'+ADMIN_PATH+'/notifications/read/'+obj.id+'"><i class="'+obj.icon+'"></i> '+obj.content+'</a></li>';
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


 //    var lock_screen_timeout;	
	// LOCK_SCREEN_TIME = LOCK_SCREEN_TIME * (60 * 1000);
	// $(function() {
	// 	$( "body" ).mousemove(function( event ) {
	// 		clearTimeout(lock_screen_timeout);
	// 		lock_screen_timeout = setTimeout(function() {
	// 		location.href = LOCK_SCREEN_URL;
	// 		},LOCK_SCREEN_TIME);
	// 	});
	// })

	
$(function(){
	$('.btn-filter-data').click(function() {
		$('#filter-data').modal('show');
	})

	$('.btn-export-data').click(function() {
		$('#export-data').modal('show');
	})

	var toggle_advanced_report_boolean = 1;
	$(".toggle_advanced_report").click(function() {
		
		if(toggle_advanced_report_boolean==1) {
			$("#advanced_export").slideDown();
			$(this).html("<i class='fa fa-minus-square-o'></i> Show Advanced Export");
			toggle_advanced_report_boolean = 0;
		}else{
			$("#advanced_export").slideUp();
			$(this).html("<i class='fa fa-plus-square-o'></i> Show Advanced Export");
			toggle_advanced_report_boolean = 1;
		}		
		
	})

	$("#table_dashboard .checkbox").click(function() {
		var is_any_checked = $("#table_dashboard .checkbox:checked").length;
		if(is_any_checked) {
			$(".btn-delete-selected").removeClass("disabled");
		}else{
			$(".btn-delete-selected").addClass("disabled");
		}
	})

	$("#table_dashboard #checkall").click(function() {
		var is_checked = $(this).is(":checked");
		$("#table_dashboard .checkbox").prop("checked",!is_checked).trigger("click");
	})

	$(".btn-delete-selected").click(function() {
		var is_any_checked = $("#table_dashboard .checkbox:checked").length;
		if(is_any_checked) {

			if(!confirm("Are you sure want to delete all selected data ?")) return false;

			var checks = [];
			$("#table_dashboard .checkbox:checked").each(function() {
				var id = $(this).val();
				checks.push(id);
			})

			show_alert_floating('Please wait whilte delete selected...');
			$.post("{{ mainpath('delete-selected') }}",{id:checks},function(resp) {				
				show_alert_floating('Delete selected successfully !');
				hide_alert_floating();
				$(".btn-reload-table").click();
			})
		}else{
			alert("Please checking any checkbox first !");
		}
	})


	$('#btn_advanced_filter').click(function() {
		$('#advanced_filter_modal').modal('show');
	})

	$(".filter-combo").change(function() {
		console.log('Filter combo detected');

		var n = $(this).val();
		var p = $(this).parents('.row-filter-combo');
		var type_data = $(this).attr('data-type');

		var filter_value = p.find('.filter-value');

		switch(n) {
			default:
				filter_value.removeAttr('placeholder').val('').prop('disabled',true);
				
				filter_value.val('').show().focus();
				p.find('.between-group').hide();

				p.find('.filter-value-between').val('').prop('disabled',true);
			break;
			case 'like':
			case 'not like':
				filter_value.val('').show().focus();	
				p.find('.between-group').hide();
				
				filter_value.attr('placeholder','e.g : Lorem Ipsum').prop('disabled',false);
			break;
			case 'asc':
				filter_value.val('').show().focus();
				p.find('.between-group').hide();

				filter_value.prop('disabled',true).attr('placeholder','Sort ascending');
			break;
			case 'desc':
				filter_value.val('').show().focus();
				p.find('.between-group').hide();

				filter_value.prop('disabled',true).attr('placeholder','Sort descending');
			break;
			case '=':
				filter_value.val('').show().focus();
				p.find('.between-group').hide();

				filter_value.prop('disabled',false).attr('placeholder','e.g : Lorem Ipsum');
			break;
			case '>=':				
				filter_value.val('').show().focus();
				p.find('.between-group').hide();

				filter_value.prop('disabled',false).attr('placeholder','e.g : 1000');
			break;
			case '<=':				
				filter_value.val('').show().focus();
				p.find('.between-group').hide();

				filter_value.prop('disabled',false).attr('placeholder','e.g : 1000');
			break;
			case '>':				
				filter_value.val('').show().focus();
				p.find('.between-group').hide();

				filter_value.prop('disabled',false).attr('placeholder','e.g : 1000');
			break;
			case '<':				
				filter_value.val('').show().focus();
				p.find('.between-group').hide();

				filter_value.prop('disabled',false).attr('placeholder','e.g : 1000');	
			break; 
			case '!=':
				filter_value.val('').show().focus();
				p.find('.between-group').hide();

				filter_value.prop('disabled',false).attr('placeholder','e.g : Lorem Ipsum');
			break;
			case 'in':
				filter_value.val('').show().focus();
				p.find('.between-group').hide();

				filter_value.prop('disabled',false).attr('placeholder','e.g : Lorem,Ipsum,Dolor Sit');
			break;
			case 'not in':
				filter_value.val('').show().focus();
				p.find('.between-group').hide();

				filter_value.prop('disabled',false).attr('placeholder','e.g : Lorem,Ipsum,Dolor Sit');
			break;
			case 'between':				
				filter_value.val('').hide();
				p.find('.between-group').show().focus();
				p.find('.filter-value-between').prop('disabled',false);
				
			break;
		}
	})

	/* Remove disabled when reload page and input value is filled */
	$(".filter-value").each(function() {
		var v = $(this).val();
		if(v != '') $(this).prop('disabled',false);
	})
	$(".filter-value-between").each(function() {
		var v = $(this).val();
		if(v != '') {
			// $(this).parents('.row-filter-combo').find('.filter-value').hide();
			$(this).prop('disabled',false);
		}
	})
})	