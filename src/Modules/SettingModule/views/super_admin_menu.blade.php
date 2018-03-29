<li class="treeview">
    <a href="#">{!! CB::icon('wrench') !!}
        <span>{{ cbTrans('settings') }}</span> {!! CB::icon('angle-right pull-right') !!}
    </a>
    <ul class="treeview-menu">
        <li class="{{ (Request::is(cbAdminPath().'/settings/add*')) ? 'active' : '' }}">
            <a href='{{route("AdminSettingsControllerGetAdd")}}'>
                {!! CB::icon('plus') !!} {{ cbTrans('Add_New_Setting') }}
            </a>
        </li>
        <?php
        $groupSetting = DB::table('cms_settings')->groupby('group_setting')->pluck('group_setting');
        ?>
        @foreach($groupSetting as $gs)
            <li class="<?=($gs == Request::get('group')) ? 'active' : ''?>">
                <a href='{{route("AdminSettingsControllerGetShow")}}?group={{urlencode($gs)}}&m=0'>
                    {!! CB::icon('wrench') !!} {{$gs}}
                </a>
            </li>
        @endforeach
    </ul>
</li>