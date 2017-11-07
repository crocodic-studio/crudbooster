<?php
$formula = $col['formula'] ?: '';
$formula_function_name = 'formula'.str_slug($name.$col['name'], '');
$script_onchange = "";
foreach ($formInput['columns'] as $c) {
    if (strpos($formula, "[{$c['name']}]") !== false) {
        $script_onchange .= "$('#{$name}{$c['name']}').change(function() { $formula_function_name(); }); ";
    }
    $formula = str_replace("[{$c['name']}]", "\$('#{$name}{$c['name']}').val()", $formula);
}
?>
@if($col['formula'])
    @push('bottom')
        <script type="text/javascript">
            function {{ $formula_function_name }}() {
                var v = {!! $formula !!};
                $('#{{$name_column}}').val(v);
            }

            $(function () {
                {!! $script_onchange !!}
            })
        </script>
    @endpush
@endif