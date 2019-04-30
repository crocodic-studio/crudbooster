
	Number.prototype.number_format = function(n, x) {
		var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
		return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
	};

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
                    minDate: '1900-01-01',
                    format: $(this).data("format")
                })
            });
		}

		if($(".datetimepicker").length > 0) {
			$(".datetimepicker").each(function () {
                $(this).daterangepicker({
                    minDate: '1900-01-01',
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

		function showConfirm(t) {
            swal({
                title: "Confirmation",
                text: "Are you sure want to do this?",
                function() {
                    location.href = t.data('href');
                }
            })
        }

		if($('a.confirmation').length > 0) {
		    $("a.confirmation").each(function () {
                let url = this.attr('href');
                this.data('href', url);
                this.attr('href','javascript:;');
                this.attr('onclick',"showConfirm(this)");
            })
        }

	});