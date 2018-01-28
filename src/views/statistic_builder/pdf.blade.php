	<script src="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/jQuery/jQuery-2.1.4.min.js') }}"></script> 
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">    
    <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
    <script src="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/datepicker/bootstrap-datepicker.js') }}" charset="UTF-8"></script>   
    <script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.bundle.js"></script>
    <script src="{{ asset ('vendor/crudbooster/assets/js/Chart.PieceLabel.js') }}"></script> 

    <script type="text/javascript">
        var lang = '{{App::getLocale()}}';

    </script>



    <style type="text/css">

        body {
		  background: rgb(204,204,204); 
		}
		page[size="A4"] {
		  background: white;
		  width: 21cm;
		  height: 29.7cm;
		  display: block;
		  margin: 0 auto;
		  margin-bottom: 0.5cm;
		  box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);
		}
		@media print {
		  body, page[size="A4"] {
		    margin: 0;
		    box-shadow: 0;
		  }
		}
    </style>



    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script type="text/javascript">
    $(function() {      

        $(".dropdown li a").click(function(){

          $(".btn:first-child #txtDateRange").text($(this).text());
          $(".btn:first-child").val($(this).text());

          if ($(this).text()=='All')
          {
            $('.datestuff').hide();
          }
          else
          {
            $('.datestuff').show();
          }

          $('.connectedSortable').empty();
          runSortables();

       });       	      
        

        var cloneSidebar = $('.control-sidebar').clone();

        @if(CRUDBooster::getCurrentMethod() == 'getBuilder')
            createSortable();        
        @endif

        function createSortable() {
            $(".connectedSortable").sortable({
                placeholder: "sort-highlight",
                connectWith: ".connectedSortable",
                handle: ".panel-heading, .inner-box, .box-header, .btn-add-widget",            
                forcePlaceholderSize: true,
                zIndex: 999999,
                stop: function(event, ui) {
                    console.log(ui.item.attr('class'));
                    var className = ui.item.attr('class');
                    var idName = ui.item.attr('id');
                    if(className == 'button-widget-area') {
                        var areaname = $('#'+idName).parent('.connectedSortable').attr('id');
                        var component = $('#'+idName+' > a').data('component');
                        console.log(areaname);
                        $('#'+idName).remove();
                        addWidget(id_cms_statistics,areaname,component);                        
                        $('.control-sidebar').html(cloneSidebar);
                        cloneSidebar = $('.control-sidebar').clone(); 
                         
                        createSortable();             
                    }
                },
                update: function(event, ui){
                    if(ui.sender){
                        var componentID = ui.item.attr('id');
                        var areaname = $('#'+componentID).parent('.connectedSortable').attr("id");
                        var index = $('#'+componentID).index();

                        
                        $.post("{{CRUDBooster::mainpath('update-area-component')}}",{componentid:componentID,sorting:index,areaname:areaname},function(response) {
                            
                        })
                    }
                }
              });
        }
           
    })
     
    </script>

    <script type="text/javascript">

        function runSortables() 
        {
            /*if ($('#txtDateRange').text()=='All')
            {
                var viewlink = "{{CRUDBooster::mainpath('view-component')}}/";
                var addon = "";
            }
            else
            {
                var viewlink = "{{CRUDBooster::mainpath('view-component-dates')}}/";
                var addon = "/"+$('#testdate1').val()+"/"+$('#testdate2').val();
            }*/
            var viewlink = "{{CRUDBooster::mainpath('view-component')}}/";
            var addon = "";
            $('.connectedSortable').each(function() {
            	console.log("in sortable");
                var areaname = $(this).attr('id');
                
                $.get("{{CRUDBooster::mainpath('list-component')}}/"+id_cms_statistics+"/"+areaname,function(response) {            
                    if(response.components) {
                        
                        $.each(response.components,function(i,obj) {
                            $('#'+areaname).append("<div id='area-loading-"+obj.componentID+"' class='area-loading'><i class='fa fa-spin fa-spinner'></i></div>");
                            $.get(viewlink+obj.componentID+addon,function(view) {
                                console.log('View For CID '+view.componentID);
                                $('#area-loading-'+obj.componentID).remove();
                                $('#'+areaname).append(view.layout);
                                
                            })
                        })                      
                    }                   
                })
            })
        }

        $(function() {


            var d = new Date();
            d.setMonth(d.getMonth() - 1);
            $('#testdate1').val(d.toJSON().slice(0,10));
        	$('#testdate2').val(new Date().toJSON().slice(0,10));
            $('.input_date').datepicker({
                dateFormat: 'yy-mm-dd',
                @if (App::getLocale() == 'ar')
                rtl: true,
                @endif
                language: lang,
                changeYear: true,
                changeMonth: true,
                onSelect: function(dateText) {
                    $('.connectedSortable').empty();
                    runSortables();
                  }
            });
            
            $('.open-datetimepicker').click(function() {
                  $(this).next('.input_date').datepicker('show');
            });

        	
        	runSortables();
            
            $(document).on('click','.btn-delete-component',function() {
            	var componentID = $(this).data('componentid');
            	var $this = $(this);

            	swal({
				  title: "Are you sure?",
				  text: "You will not be able to recover this widget !",
				  type: "warning",
				  showCancelButton: true,
				  confirmButtonColor: "#DD6B55",
				  confirmButtonText: "Yes",
				  closeOnConfirm: true
				},
				function(){
				  	
	            	$.get("{{CRUDBooster::mainpath('delete-component')}}/"+componentID,function() {
	            		$this.parents('.border-box').remove();
	            		
	            	});
				});
            	
            })
            $(document).on('click','.btn-edit-component',function() {
				var componentID = $(this).data('componentid');
				var name        = $(this).data('name');

            	$('#modal-statistic .modal-title').text(name);
            	$('#modal-statistic .modal-body').html("<i class='fa fa-spin fa-spinner'></i> Please wait loading...");
            	$('#modal-statistic').modal('show');

            	$.get("{{CRUDBooster::mainpath('edit-component')}}/"+componentID,function(response) {
                    $('#modal-statistic .modal-body').html(response);
                })
            })

            $('#modal-statistic .btn-submit').click(function() {         
                
                $('#modal-statistic form .has-error').removeClass('has-error');

                var required_input = [];
                $('#modal-statistic form').find('input[required],textarea[required],select[required]').each(function() {
                    var $input = $(this);
                    var $form_group = $input.parent('.form-group');
                    var value = $input.val();

                    if(value == '') {
                        required_input.push($input.attr('name'));
                    }
                })    

                if(required_input.length) {  
                    setTimeout(function() {
                        $.each(required_input,function(i,name) {
                            $('#modal-statistic form').find('input[name="'+name+'"],textarea[name="'+name+'"],select[name="'+name+'"]').parent('.form-group').addClass('has-error');
                        })
                    },200);                  
                    
                    return false;
                }

            	var $button = $(this).text('Saving...').addClass('disabled');
            	
            	$.ajax({
            		data:$('#modal-statistic form').serialize(),
            		type:'POST',
            		url:"{{CRUDBooster::mainpath('save-component')}}",
            		success:function() {
            			
            			$button.removeClass('disabled').text('Save Changes');
            			$('#modal-statistic').modal('hide');
            			window.location.href = "{{Request::fullUrl()}}";
            		},
            		error:function() {
            			alert('Sorry something went wrong !');
            			$button.removeClass('disabled').text('Save Changes');
            		}
            	})
            })
        })
    </script>
    
        
        <!--<div class="statistic-row row">-->
            <page size="A4" class="statistic-row row"> 
            	<H1>Test Report </h1>
            	<div id='area1' class="col-sm-12 connectedSortable">            	

	            </div>
	            <div id='area2' class="col-sm-12 connectedSortable">
	               
	            </div>
	            <div id='area3' class="col-sm-12 connectedSortable">

	            </div>
	            <div id='area4' class="col-sm-12 connectedSortable">
	            	
	            </div>    
            </page>            
        <!--</div>

        <div class='statistic-row row'>
                <div id='area5' class="col-sm-12 connectedSortable">
 
                </div>
        </div>-->
    
