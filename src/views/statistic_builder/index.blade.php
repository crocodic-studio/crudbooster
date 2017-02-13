	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">    
    <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>


    <script type="text/javascript">
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $(document).ajaxStart(function() {
                $('.btn-save-statistic').html("<i class='fa fa-spin fa-spinner'></i>");
            })
            $(document).ajaxStop(function() {
                $('.btn-save-statistic').html("<i class='fa fa-save'></i> Auto Save Ready");
            })

            $('.btn-show-sidebar').click(function(e)  {
                e.stopPropagation();
            })
            $('html,body').click(function() {
                $('.control-sidebar').removeClass('control-sidebar-open');
            })
        })
    </script>
    <style type="text/css">
        .control-sidebar ul {
            padding:0 0 0 0;
            margin:0 0 0 0;            
            list-style-type:none;
        }
        .control-sidebar ul li {
            text-align: center;
            padding: 10px;            
            border-bottom: 1px solid #555555;
        }
        .control-sidebar ul li:hover {
            background: #555555;
        }
        .control-sidebar ul li .title {
            text-align: center;
            color: #ffffff;            
        }
        .control-sidebar ul li img {
            width: 100%;            
        }

        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
         
        ::-webkit-scrollbar-track {            
            background: #000000;
        }
         
        ::-webkit-scrollbar-thumb {
            background: #333333;          
        }
    </style>

	<!-- ADDITION FUNCTION FOR BUTTON -->
	<script type="text/javascript">
        var id_cms_statistics = '{{$id_cms_statistics}}';

        function addWidget(id_cms_statistics,area,component) {      
            var id = new Date().getTime();
            $('#'+area).append("<div id='"+id+"' class='area-loading'><i class='fa fa-spin fa-spinner'></i></div>");

            var sorting = $('#'+area+' .border-box').length;             
            $.post("{{CRUDBooster::mainpath('add-component')}}",{component_name:component,id_cms_statistics:id_cms_statistics,sorting:sorting,area:area},function(response) {
                $('#'+area).append(response.layout);   
                $('#'+id).remove();                
            })
        }

	</script>
	<!--END HERE-->


	<!-- jQuery UI 1.11.4 -->
    <style type="text/css">
        .sort-highlight {
            border:3px dashed #cccccc;                    
        }
        .layout-grid {
            border:1px dashed #cccccc;
            min-height: 150px;
        }
        .layout-grid + .layout-grid {
            border-left:1px dashed transparent;            
        }
        .border-box {        	
        	position: relative;        	
        }
        .border-box .action {        	
        	font-size: 20px;
        	display: none;
        	text-align: center;
        	display: none;
        	padding:3px 5px 3px 5px;
        	background:#DD4B39;
        	color:#ffffff;
        	width: 70px;
        	-webkit-border-bottom-right-radius: 5px;
			-webkit-border-bottom-left-radius: 5px;
			-moz-border-radius-bottomright: 5px;
			-moz-border-radius-bottomleft: 5px;
			border-bottom-right-radius: 5px;
			border-bottom-left-radius: 5px;
			position: absolute;
			margin-top: -20px;			
			right: 0;
			z-index: 999;
			opacity: 0.8;	
        }
        .border-box .action a {
        	color: #ffffff;
        }
        
        .border-box:hover {
        	/*border:2px dotted #BC3F30;*/
        }
        
        @if(CRUDBooster::getCurrentMethod() == 'getBuilder')
        .border-box:hover .action {
        	display: block;
        }
        .panel-heading, .inner-box, .box-header, .btn-add-widget {
            cursor: move;
        }
        @endif
        
        .connectedSortable {
        	position: relative;
        }
        .area-loading {        
        	position: relative;	
        	width: 100%;  
        	height: 130px;      	
        	background: #dedede;
        	border: 4px dashed #cccccc;
        	font-size: 50px;
        	color: #aaaaaa;
        	margin-bottom: 20px;
        }
        .area-loading i {        	
        	position: absolute;
        	left:45%;
        	top:30%;        	
        	transform: translate(-50%, -50%);        	      
        }
    </style>
    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script type="text/javascript">
    $(function() {             	       

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
        $(function() {
        	
        	$('.connectedSortable').each(function() {
        		var areaname = $(this).attr('id');
        		
        		$.get("{{CRUDBooster::mainpath('list-component')}}/"+id_cms_statistics+"/"+areaname,function(response) {       		
        			if(response.components) {
        				
        				$.each(response.components,function(i,obj) {
        					$('#'+areaname).append("<div id='area-loading-"+obj.componentID+"' class='area-loading'><i class='fa fa-spin fa-spinner'></i></div>");
        					$.get("{{CRUDBooster::mainpath('view-component')}}/"+obj.componentID,function(view) {
        						console.log('View For CID '+view.componentID);
        						$('#area-loading-'+obj.componentID).remove();
        						$('#'+areaname).append(view.layout);
        						
        					})
        				})        				
        			}       			
        		})
        	})
        	       
            
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

    <div id='modal-statistic' class="modal fade" tabindex="-1" role="dialog">
	  <div class="modal-dialog" role="document">
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
	        <button type="button" class="btn-submit btn btn-primary" data-loading-text="Saving..." autocomplete="off">Save changes</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

    <div id='statistic-area'>


        <div class="statistic-row row">
            <div id='area1' class="col-sm-3 connectedSortable">            	

            </div>
            <div id='area2' class="col-sm-3 connectedSortable">
               
            </div>
            <div id='area3' class="col-sm-3 connectedSortable">

            </div>
            <div id='area4' class="col-sm-3 connectedSortable">
            	
            </div>            
        </div>

        <div class='statistic-row row'>
                <div id='area5' class="col-sm-12 connectedSortable">
 
                </div>
        </div>
    
    </div><!--END STATISTIC AREA-->
