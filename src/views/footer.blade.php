<footer class="main-footer">
    <!-- To the right -->
    <div class="pull-{{ trans('crudbooster.right') }} hidden-xs">
        {{ trans('crudbooster.powered_by') }} {{CRUDBooster::getSetting('appname')}}
    </div>
    <!-- Default to the left -->
    <strong>{{ trans('crudbooster.copyright') }} &copy; <?php echo date('Y') ?>. {{ trans('crudbooster.all_rights_reserved') }} .</strong>
</footer>
