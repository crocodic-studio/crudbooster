@foreach($addaction as $a)
    <?php
    foreach ($row as $key => $val) {
        $a['url'] = str_replace("[$key]", $val, $a['url']);
    }

    $confirm_box = '';
    if (isset($a['confirmation']) && ! empty($a['confirmation']) && $a['confirmation']) {

        $a['confirmation_title'] = ! empty($a['confirmation_title']) ? $a['confirmation_title'] : cbTrans('confirmation_title');
        $a['confirmation_text'] = ! empty($a['confirmation_text']) ? $a['confirmation_text'] : cbTrans('confirmation_text');
        $a['confirmation_type'] = ! empty($a['confirmation_type']) ? $a['confirmation_type'] : 'warning';
        $a['confirmation_showCancelButton'] = empty($a['confirmation_showCancelButton']) ? 'true' : 'false';
        $a['confirmation_confirmButtonColor'] = ! empty($a['confirmation_confirmButtonColor']) ? $a['confirmation_confirmButtonColor'] : '#DD6B55';
        $a['confirmation_confirmButtonText'] = ! empty($a['confirmation_confirmButtonText']) ? $a['confirmation_confirmButtonText'] : cbTrans('confirmation_yes');;
        $a['confirmation_cancelButtonText'] = ! empty($a['confirmation_cancelButtonText']) ? $a['confirmation_cancelButtonText'] : cbTrans('confirmation_no');;
        $a['confirmation_closeOnConfirm'] = empty($a['confirmation_closeOnConfirm']) ? 'true' : 'false';

        $confirm_box = '
        swal({   
            title: "'.$a['confirmation_title'].'",
            text: "'.$a['confirmation_text'].'",
            type: "'.$a['confirmation_type'].'",
            showCancelButton: '.$a['confirmation_showCancelButton'].',
            confirmButtonColor: "'.$a['confirmation_confirmButtonColor'].'",
            confirmButtonText: "'.$a['confirmation_confirmButtonText'].'",
            cancelButtonText: "'.$a['confirmation_cancelButtonText'].'",
            closeOnConfirm: '.$a['confirmation_closeOnConfirm'].', },
            function(){  location.href="'.$a['url'].'"});

        ';
    }

    $label = $a['label'];
    $icon = $a['icon'];
    $color = $a['color'] ?: 'primary';
    $confirmation = $a['confirmation'];


    $url = $a['url'];
    if (isset($confirmation) && ! empty($confirmation)) {
        $url = "javascript:;";
    }

    if (isset($a['showIf'])) {

        $query = $a['showIf'];

        foreach ($row as $key => $val) {
            $query = str_replace("[".$key."]", '"'.$val.'"', $query);
        }

        @eval("if($query) {
        echo \"<a class='btn btn-xs btn-\$color' title='\$label' onclick='\$confirm_box' href='\$url'>
        <i class='\$icon'></i>
         $label</a>&nbsp;\";
    }");
    } else {
        echo "<a class='btn btn-xs btn-$color' title='$label' onclick='$confirm_box' href='$url'>
        <i class='$icon'></i>

        $label</a>&nbsp;";
    }
    ?>
@endforeach