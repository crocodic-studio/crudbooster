<footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
        {{ trans('cb::cb.powered_by') }} {{ cb()->getAppName() }}
    </div>
    <!-- Default to the left -->
    <strong>{!! trans('cb::cb.copyright',['year'=>date('Y')]) !!}.</strong>
</footer>
