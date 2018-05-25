swal({
title: "{!! cbTrans('delete_title_confirm') !!}",
text: "{!! cbTrans('delete_description_confirm') !!}",
type: "warning",
showCancelButton: true,
confirmButtonColor: "#ff0000",
confirmButtonText: "{!! cbTrans('confirmation_yes') !!}",
cancelButtonText: "{!! cbTrans('confirmation_no') !!}",
closeOnConfirm: false },
function(){  location.href="{!! $redirectTo !!}" });