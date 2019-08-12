
	Number.prototype.number_format = function(n, x) {
		var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
		return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
	};

    function showConfirmation(title, message, callback) {
        swal({
            title: title,
            text: message,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
            .then((willDelete) => {
                if (willDelete) {
                    callback()
                }
            });
    }

    function showConfirm(title, msg, callback) { showConfirmation(title, msg, callback) }

	$(function() {

		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		$('input[type=text]').first().not(".not_focus,.datepicker,.datetimepicker").focus();

		if($(".datepicker").length > 0) {
		    $(".datepicker").each(function () {
                $(this).daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    minDate: '1970-01-01',
                    format: $(this).data("format")
                })
            });
		}

		if($(".datetimepicker").length > 0) {
			$(".datetimepicker").each(function () {
                $(this).daterangepicker({
                    minDate: '1970-01-01',
                    singleDatePicker: true,
                    showDropdowns: true,
                    timePicker:true,
                    timePicker12Hour: false,
                    timePickerIncrement: 5,
                    timePickerSeconds: true,
                    autoApply: true,
                    format: $(this).data("format")
                })
            });
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