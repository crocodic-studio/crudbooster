@if(Request::input('fileformat') == 'pdf')
    <h3>{{Request::input('filename')}}</h3>
@endif
<table border='1' width='100%' cellpadding='3' cellspacing="0" style='border-collapse: collapse;font-size:12px'>
    <thead>
    <tr>
        @foreach ($columns as $col)
            @if (request('columns') && ! in_array($col['name'], request('columns')))
                @continue
            @endif
            <th style='background:#eeeeee'>{!! $col['label'] !!}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @if(empty($result))
        <tr class='warning'>
            <td colspan='{{count($columns)+1}}' align="center">No Data Avaliable</td>
        </tr>
    @else
        @foreach($result as $row)
            <tr>
                @foreach ($columns as $col)
                    @if (request('columns') && ! in_array($col['name'], request('columns')))
                        @continue
                    @endif
                    <?php
                    $value = @$row->{$col['field']};
                    $title = @$row->{$title_field};
                    ?>
                    @if (@$col['image'])
                        <?php
                        if ($value == '') {
                            $value = "http://placehold.it/50x50&text=NO+IMAGE";
                        }
                        $pic = (strpos($value, 'http://') !== FALSE) ? $value : asset($value);
                        $pic_small = $pic;
                        ?>
                        @if (request('fileformat') == 'pdf')
                            <td><a data-lightbox='roadtrip' rel='group_{{$table}}' title='{!! $col['label'] !!}: {!! $title !!}' href='"{!! $pic !!}"'>
                                    <img class='img-circle' width='40px' height='40px' src='"{!! $pic_small !!}"'/></a></td>
                        @else
                            <td>{!! $pic !!}</td>
                        @endif
                    @elseif (@$col['download'])
                        <?php $url = (strpos($value, 'http://') !== FALSE) ? $value : asset($value); ?>
                        <td><a class='btn btn-sm btn-primary' href='{!! $url !!}' target='_blank' title='Download File'>Download</a></td>
                    @else
                        <?php
                        //limit character
                        if ($col['str_limit']) {
                            $value = trim(strip_tags($value));
                            $value = str_limit($value, $col['str_limit']);
                        }

                        if ($col['nl2br']) {
                            $value = nl2br($value);
                        }
                        ?>
                        @if (Request::input('fileformat') == 'pdf')
                            @if (! empty($col['callback_php']))
                                <?php
                                foreach ($row as $k => $v) {
                                    $col['callback_php'] = str_replace("[".$k."]", $v, $col['callback_php']);
                                }
                                @eval("\$value = ".$col['callback_php'].";");
                                ?>
                            @endif
                            {{--New method for callback--}}
                            @if (isset($col['callback'])) <?php $value = call_user_func($col['callback'], $row); ?> @endif
                        @endif

                        <td>{!! $value !!}</td>
                    @endif
                @endforeach
            </tr>
        @endforeach
    @endif
    </tbody>
</table>
<script type="text/php">
    if ( isset($pdf) ) {
        $font = Font_Metrics::get_font("helvetica", "bold");
        $pdf->page_text(36, 18, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 6, array(0,0,0));
    }


</script>