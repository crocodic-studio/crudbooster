
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
                $(this).datetimepicker({
                    timepicker: false,
                    format: $(this).data("format")
                })
            });
		}

		if($(".datetimepicker").length > 0) {
			$(".datetimepicker").each(function () {
                $(this).datetimepicker({
                    format: $(this).data("format")
                })
            });
		}

		//Timepicker
		if($(".timepicker").length > 0) {
			$(".timepicker").datetimepicker({
			  datepicker: false
			});
		}

        $(".datatable").dataTable({
            initComplete: function() {
                $(this.api().table().container()).find('input').parent().wrap('<form>').parent().attr('autocomplete', 'off');
            }
        });
        $(".select2").select2();
	});