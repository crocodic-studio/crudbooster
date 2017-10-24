<!-- Bootstrap 3.3.2 -->
<link href="{{ asset("vendor/crudbooster/assets/adminlte/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet"
      type="text/css"/>
<!-- Font Awesome Icons -->
<link href="{{asset("vendor/crudbooster/assets/adminlte/font-awesome/css")}}/font-awesome.min.css" rel="stylesheet"
      type="text/css"/>
<!-- Ionicons -->
<link href="{{asset("vendor/crudbooster/ionic/css/ionicons.min.css")}}" rel="stylesheet" type="text/css"/>
<!-- Theme style -->
<link href="{{ asset("vendor/crudbooster/assets/adminlte/dist/css/AdminLTE.min.css")}}" rel="stylesheet"
      type="text/css"/>
<link href="{{ asset("vendor/crudbooster/assets/adminlte/dist/css/skins/_all-skins.min.css")}}" rel="stylesheet"
      type="text/css"/>

@include('crudbooster::admin_template_plugins')

<form method='get' action="">
    {!! CRUDBooster::getUrlParameters(['q']) !!}
    <input type="text" placeholder="{{cbTrans('datamodal_search_and_enter')}}" name="q"
           title="{{cbTrans('datamodal_enter_to_search')}}" value="{{Request::get('q')}}" class="form-control">
</form>

<table id='table_dashboard' class='table table-striped table-bordered table-condensed' style="margin-bottom: 0px">
    <thead>
    @php $columns_alias = explode(',',$data['columns_alias']); @endphp
    @foreach($columns_alias as $column_alias)
        <th>{{ $column_alias }}</th>
    @endforeach
    <th width="5%">{{cbTrans('datamodal_select')}}</th>
    </thead>
    <tbody>
    @foreach($result as $row)
        <tr>
            @php $columns = explode(',',$data['columns']); @endphp
            @foreach($columns as $col)
                <?php
                $img_extension = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
                $ext = pathinfo($row->$col, PATHINFO_EXTENSION);
                if ($ext && in_array($ext, $img_extension)) {
                    echo "<td><a href='".asset($row->$col)."' data-lightbox='roadtrip'><img src='".asset($row->$col)."' width='50px' height='30px'/></a></td>";
                } else {
                    echo "<td>".str_limit(strip_tags($row->$col), 50)."</td>";
                }
                ?>
            @endforeach
            <td>
                <a class='btn btn-primary' href='javascript:void(0)'
                   onclick='parent.selectDataModal{{$name}}("{{ $col->{$data['column_label'] }}","{{ $col->{$data['column_value']} }}")'>
                    <i class='fa fa-check-circle'></i>
                    {{cbTrans('datamodal_select')}}
                </a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<div align="center">{!! str_replace("/?","?",$result->appends(Request::all())->render()) !!}</div>