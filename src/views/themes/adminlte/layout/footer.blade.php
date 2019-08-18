<footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
        {{ cbLang('powered_by') }} {{ cb()->getAppName() }}
    </div>
    <!-- Default to the left -->
    <strong>{!! cbLang('copyright',['year'=>date('Y')]) !!}.</strong>
</footer>
