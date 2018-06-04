// request permission on page load

Number.prototype.number_format = function (n, x) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
    return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
};

$(function () {

    jQuery.fn.outerHTML = function (s) {
        return s
            ? this.before(s).remove()
            : jQuery("<p>").append(this.eq(0).clone()).html();
    };


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.treeview').each(function () {
        var active = $(this).find('.active').length;
        if (active) {
            $(this).addClass('active');
        }
    })


    $('input[type=text]').first().not(".notfocus").focus();

    if ($(".datepicker").length > 0) {
        $('.datepicker').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            minDate: '1900-01-01',
            format: 'YYYY-MM-DD'
        })
    }

    if ($(".datetimepicker").length > 0) {
        $(".datetimepicker").daterangepicker({
            minDate: '1900-01-01',
            singleDatePicker: true,
            showDropdowns: true,
            timePicker: true,
            timePicker12Hour: false,
            timePickerIncrement: 5,
            timePickerSeconds: true,
            autoApply: true,
            format: 'YYYY-MM-DD HH:mm:ss'
        })
    }

    //Timepicker
    if ($(".timepicker").length > 0) {
        $(".timepicker").timepicker({
            showInputs: true,
            showSeconds: true,
            showMeridian: false
        });
    }

});
