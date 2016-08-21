@include("crudbooster::emails.header")

<p>Hello,</p>
<p>Someone with IP Address {{$_SERVER['REMOTE_ADDR']}} at {{date('Y-m-d H:i:s')}} has been requested password, the following is your new password : </p>
<p>Password : {{$password}}</p>

@include("crudbooster::emails.footer")