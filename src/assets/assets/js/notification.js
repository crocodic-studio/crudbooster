document.addEventListener('DOMContentLoaded', function () {
    if (!Notification) {
        alert('Desktop notifications not available in your browser. Try Chromium.');
        return;
    }

    if (Notification.permission !== "granted")
        Notification.requestPermission();
});

function beep() {

    $("#sound_beep").remove();
    $('body').append('<audio id="sound_beep" style="display:none" autoplay>'+
        +'<source src="'+ASSET_URL+'/vendor/crudbooster/assets/sound/bell_ring.ogg" type="audio/ogg">'
        +'<source src="'+ASSET_URL+'/vendor/crudbooster/assets/sound/bell_ring.mp3" type="audio/mpeg">'
        +'Your browser does not support the audio element.</audio>');
}

function send_notification(text,url) {
    if (Notification.permission !== "granted") {
        console.log("Request a permission for Chrome Notification");
        Notification.requestPermission();
    } else {
        var notification = new Notification(APP_NAME + ' Notification', {
            icon: 'https://cdn1.iconfinder.com/data/icons/CrystalClear/32x32/actions/agt_announcements.png',
            body: text,
            'tag': text
        });
        console.log("Send a notification");
        beep();

        notification.onclick = function () {
            location.href = url;
        };
    }
}
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