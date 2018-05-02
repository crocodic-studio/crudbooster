<div class='btn-group btn-group-action'>
    <button type='button' class='btn btn-xs btn-primary btn-action'>{{cbTrans("action_label")}}</button>
    <button type='button' class='btn btn-xs btn-primary dropdown-toggle' data-toggle='dropdown'>
        <span class='caret'></span>
        <span class='sr-only'>Toggle Dropdown</span>
    </button>
    <ul class='dropdown-menu dropdown-menu-action' role='menu'>
        @foreach($addAction as $a)
            <?php
            foreach ($row as $key => $val) {
                $a['url'] = str_replace("[".$key."]", $val, $a['url']);
            }

            $label = $a['label'];
            $url = $a['url']."?return_url=".urlencode(Request::fullUrl());
            $icon = $a['icon'];
            $color = $a['color'] ?: 'primary';

            if (isset($a['showIf'])) {

                $query = $a['showIf'];

                foreach ($row as $key => $val) {
                    $query = str_replace("[".$key."]", '"'.$val.'"', $query);
                }

                @eval("if($query) {
                    echo \"<li><a title='\$label' href='\$url'>
                    <i class='\$icon'></i>
                     \$label</a></li>\";
                }");
            } else {
                echo "
                <li><a title='$label' href='$url'>
                        <i class='$icon'></i>
                        $label</a></li>";
            }
            ?>
        @endforeach

        @if(CRUDBooster::canRead() && $buttonDetail)
            <li><a class='btn-detail' title='{{cbTrans("action_detail_data")}}'
                   href='{{CRUDBooster::mainpath("detail/".$row->$pk)."?return_url=".urlencode(Request::fullUrl())}}'>
                    {!! cbIcon('eye') !!}
                    {{cbTrans("action_detail_data")}}</a></li>
        @endif

        @if(CRUDBooster::canUpdate() && $button_edit)
            <li><a class='btn-edit' title='{{cbTrans("action_edit_data")}}'
                   href='{{CRUDBooster::mainpath("edit/".$row->$pk)."?return_url=".urlencode(Request::fullUrl())."&parent_id=".g("parent_id")."&parent_field=".$parent_field}}'>
                    {!! cbIcon('pencil') !!}
                    {{cbTrans("action_edit_data")}}</a></li>
        @endif

        @if(CRUDBooster::canDelete() && $deleteBtn)
            <?php $url = CRUDBooster::mainpath("delete/".$row->$pk);?>
            <li><a class='btn-delete' title='{{cbTrans("action_delete_data")}}' href='javascript:;'
                   onclick='{{CRUDBooster::deleteConfirm($url)}}'>
                    {!! cbIcon('trash') !!}
                    {{cbTrans("action_delete_data")}}</a></li>
        @endif
    </ul>
</div>