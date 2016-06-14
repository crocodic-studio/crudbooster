@include("emails/header")

<p>Halo,</p>
<p>Seseorang dengan IP Address {{$_SERVER['REMOTE_ADDR']}} pada pukul {{date('Y-m-d H:i:s')}} telah melakukan permintaan password, berikut ini password baru anda : </p>
<p>Password : {{$password}}</p>

@include("emails/footer")