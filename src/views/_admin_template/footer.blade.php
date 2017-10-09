<footer class="main-footer">
    <!-- To the right -->
    <div class="pull-{{ cbTrans('right') }} hidden-xs">
        {{ cbTrans('powered_by') }} {{CRUDBooster::getSetting('appname')}}
    </div>
    <!-- Default to the left -->
    <strong>{{ cbTrans('copyright') }} &copy; {!! date('Y')  !!} {{ cbTrans('all_rights_reserved') }} .</strong>
</footer>
