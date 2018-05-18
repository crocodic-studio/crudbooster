<div class="user-panel">
    <div class="pull-{{ cbTrans('left') }} image">
        <img src="{{ auth('cbAdmin')->user()->myPhoto() }}" style="width:45px;height:45px;" class="img-circle" alt="{{ cbTrans('user_image') }}"/>
    </div>
    <div class="pull-{{ cbTrans('left') }} info">
        <p>{{ auth('cbAdmin')->user()->name }}</p>
        <!-- Status -->
        <a href="#">{!! cbIcon('circle text-success') !!} {{ cbTrans('online') }}</a>
    </div>
</div>