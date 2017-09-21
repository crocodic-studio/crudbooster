<label>{{ cbTrans('chose_theme_color')}}</label>
<select name='theme_color' class='form-control' required>
    <option value=''>{{ cbTrans('chose_theme_color_select')}}</option>
    <?php
    $skins = array(
        'skin-blue',
        'skin-blue-light',
        'skin-yellow',
        'skin-yellow-light',
        'skin-green',
        'skin-green-light',
        'skin-purple',
        'skin-purple-light',
        'skin-red',
        'skin-red-light',
        'skin-black',
        'skin-black-light'
    );
    ?>
    @foreach($skins as $skin)
        <option {!! ($role->theme_color==$skin)?"selected":"" !!} value='{{ $skin }}'>
            {!! ucwords(str_replace('-', ' ', $skin)) !!}
        </option>
    @endforeach
</select>
<div class="text-danger">{{ $errors->first('theme_color') }}</div>
@push('bottom')
    <script type="text/javascript">
        $(function () {
            $("select[name=theme_color]").change(function () {
                var n = $(this).val();
                $("body").attr("class", n);
            })

            $('#set_as_superadmin input').click(function () {
                var n = $(this).val();
                if (n == '1') {
                    $('#privileges_configuration').hide();
                } else {
                    $('#privileges_configuration').show();
                }
            })

            $('#set_as_superadmin input:checked').trigger('click');
        })
    </script>
@endpush