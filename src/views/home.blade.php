@extends('crudbooster::admin_template')

@section('content')
    <?php if(!Session::get('dashboard_config_mode')):?>
    <style type="text/css">       
        #dashboard .box-tools button {display: none}
        #dashboard .inner a {display: none}        
    </style>
    <?php endif;?>

    <!-- Morris.js charts -->
    <script src="http://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="<?=asset("vendor/crudbooster/assets/adminlte/plugins/morris/morris.min.js")?>" type="text/javascript"></script>
    <!-- Morris chart -->
    <link href="<?=asset("vendor/crudbooster/assets/adminlte/plugins/morris/morris.css")?>" rel="stylesheet" type="text/css" />

    <script type="text/javascript">
      $(function() {
        var dataid = <?php echo json_encode($list_id_dashboard)?>;
        $.each(dataid,function(i,id) {
            $("#dashboard").append("<div id='dashboard_"+id+"' class='col-sm-3'><div class='small-box'><div class='inner'><i class='fa fa-spinner fa-spin'></i> Loading...</div></div></div>");
            $.get("{{url(config('crudbooster.ADMIN_PATH'))}}/statistic-dashboard/"+id,function(html_widget) {
                $("#dashboard_"+id).replaceWith(html_widget);
            })
        }) 
      })
    </script>
    <div id='dashboard'>

    </div>

    <div class='clearfix'></div>

    <div id="modal_widget_statistic" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Modal title</h4>
            </div>
            <div class="modal-body">
              <p>One fine body&hellip;</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="button" class="btn-add btn btn-primary">Add</button>
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
      </div><!-- /.modal -->
@endsection