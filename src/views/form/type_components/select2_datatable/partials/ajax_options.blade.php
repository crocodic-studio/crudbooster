@if($value)
    @php
    $options = [];
    foreach(DB::table($select_table)->get() as $i => $r):
        $options[$i]['value'] = $r->$select_value;

        if($formInput['options']['format']) {
             $options[$i]['label'] = $formInput['options']['format'];
            foreach($r as $k => $v) {
                $options[$i]['label'] = str_replace("[$k]", $v, $options[$i]['label']);
            }
        }else{
            $options[$i]['label'] = $r->$select_label;
        } // duplicated logic


    endforeach
    @endphp

    @foreach($options as $option)
        <option value="{{$option['value']}}" {{ findSelected($value, $form, $option['value']) }} >{{$option['label']}}</option>
    @endforeach
@endif