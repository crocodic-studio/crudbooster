<div class="user-panel">
    <div class="pull-{{ cbTrans('left') }} image">
        <img src="{{ CRUDBooster::myPhoto() }}" style="width:45px;height:45px;" class="img-circle" alt="{{ cbTrans('user_image') }}"/>
    </div>
    <div class="pull-{{ cbTrans('left') }} info">
        <p>{{ CRUDBooster::myName() }}</p>
        <!-- Status -->
        <a href="#">{!! CB::icon('circle text-success') !!} {{ cbTrans('online') }}</a>
    </div>
</div>