@extends('admin/admin_template')

@section('content')
    <script>
    $(function() {
      jQuery.fn.selectText = function(){
         var doc = document;
         var element = this[0];
         console.log(this, element);
         if (doc.body.createTextRange) {
             var range = document.body.createTextRange();
             range.moveToElementText(element);
             range.select();
         } else if (window.getSelection) {
             var selection = window.getSelection();        
             var range = document.createRange();
             range.selectNodeContents(element);
             selection.removeAllRanges();
             selection.addRange(range);
         }
      };

    $(document).on("click",".selected_text",function() {
          console.log("clicked");
          $(this).selectText();
     });

    })
    </script> 
    <style>.selected_text {cursor:pointer;} .selected_text:hover {color:#76a400}</style>
    <div class='row'>
      <div class='col-sm-12'>

            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">TABEL LIST</h3>
                    <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                  </div><!-- /.box-tools -->
                </div>
                <div class="box-body">
                    <table class='table table-striped table-api table-bordered'>
                        <thead>
                            <tr class='info'><th width='2%'>No</th><th>Nama Tabel</th><th width='15%'>Generator</th></tr>
                        </thead> 
                        <tbody>
                              <?php 
                                  $no=1;
                                  foreach($tables as $tb):
                              ?>
                            <tr>  
                                <td><?php echo $no++;?></td>
                                <td><?php echo $tb?></td>
                                <td>
                                    <a class='btn btn-primary btn-generate' href='javascript:void(0)' data-table="{{$tb}}" data-url="{{action("ControllerGeneratorController@getGenerate").'/'.$tb}}">Generate</a> 
                                </td>
                            </tr>
                              <?php  endforeach;?>
                              <?php if(count($tables)==0):?>
                                <tr><td colspan='3' align="center">There is no data</td></tr>
                              <?php endif;?>
                        </tbody>
                    </table>
                </div>
            </div>

      </div>    
    </div>

    <script>
    $(function() {
      $(".btn-generate").each(function() {
          var table = $(this).data('table');
          var h = $(this);
          $.get("{{action('ControllerGeneratorController@getIsGenerate')}}/"+table,function(resp) {
              if(resp.status == 1) {
                h.removeClass('btn-primary');
                h.addClass('btn-success');  
                h.addClass('disabled');
                h.text("Already");
              }
          })
      })

      $(".btn-generate").click(function() {
          $(this).text('Wait Generating...');
          var u = $(this).data('url');
          var h = $(this);
          $.get(u,function(resp) {
              if(resp == 1) {
                h.removeClass('btn-primary');
                h.addClass('btn-success');  
                h.addClass('disabled');
                h.text("Already");
              }else{
                h.text("Already");
                alert('Controller Already');
              }              
          })
      })
      $(".link_name_api").click(function() {
          $(".detail_api").slideUp();
          $(this).parent("td").find(".detail_api").slideDown();
      })
      $(".selected_text").each(function() {
            var n = $(this).text();
            if(n.indexOf('api_')==0) {
                $(this).attr('class','selected_text text-danger');
            }            
      })
    })
    </script> 
  
@endsection