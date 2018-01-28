	<link rel="stylesheet" href="{{ asset ('vendor/crudbooster/assets/adminlte/bootstrap/css/bootstrap.min.css') }}">    
	<script src="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/jQuery/jQuery-2.1.4.min.js') }}"></script> 
	<script src="{{ asset ('vendor/crudbooster/assets/adminlte/bootstrap/js/bootstrap.min.js') }}"></script> 
    <script src="{{ asset ('vendor/crudbooster/assets/js/html2pdf.bundle.min.js') }}"></script> 
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
		}
		page[size="A4"] {
		  background: white;
		  width: 21cm;
		  /*height: 29.7cm;*/
		  display: block;
		  margin: 0 auto;
		  margin-bottom: 0.5cm;
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
    	var id_cms_statistics = '{{$id_cms_statistics}}';

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
    function makeid() {
      var text = "";
      var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

      for (var i = 0; i < 15; i++)
        text += possible.charAt(Math.floor(Math.random() * possible.length));

      return text;
    }

        function runSortables() 
        {
        	var id_cms_statistics = '{{$id_cms_statistics}}';
        	var totalcount = 0;
        	var loadcount =0;
            var smallcount=0;
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
                var areaname = $(this).attr('id');

                
                $.get("{{CRUDBooster::mainpath('list-component')}}/"+id_cms_statistics+"/"+areaname,function(response) {            
                    if(response.components) {
                		totalcount = totalcount + response.components.length;
                		console.log(totalcount);        
                        $.each(response.components,function(i,obj) {                        	
                            $('#'+areaname).append("<div id='area-loading-"+obj.componentID+"' class='area-loading'><i class='fa fa-spin fa-spinner'></i></div>");
                            $.get(viewlink+obj.componentID+addon,function(view) {                                
                                $('#area-loading-'+obj.componentID).remove();
                                $('#'+areaname).append(view.layout);
                                if (areaname=="area5")
                                {
                                    if (loadcount==0)
                                    {                                        
                                        loadcount = loadcount +2;
                                    }
                                    else 
                                    {
                                        loadcount = loadcount+1;
                                        if ((loadcount % 3==0)&&(loadcount!=0)) $('#'+areaname).append('<div class="html2pdf__page-break"></div>');        
                                    }   
                                    
                                    console.log("loadcount");
                                    console.log(loadcount);
                                }
                                smallcount = smallcount + 1;
                                if (smallcount>=totalcount)
                                {
                                    console.log("finished smallcount");
                                    setTimeout(function(){ html2pdf(document.getElementById('pagereport'),{filename: makeid()+'.pdf',}); }, 2000);
                                    
                                }
                                
                            })
                        })                      
                    }                   
                })
            })
        }

        $(function() {

        	
        	runSortables();
            
        })
    </script>
    
        
        <!--<div class="statistic-row row">-->
            <page id="pagereport" size="A4" class="statistic-row row"> 
            	<H1>Test Report </h1>
            	<div id='area1' class="col-sm-12 connectedSortable">            	

	            </div>
	            <div id='area2' class="col-sm-12 connectedSortable">
	               
	            </div>
	            <div id='area3' class="col-sm-12 connectedSortable">

	            </div>
	            <div id='area4' class="col-sm-12 connectedSortable">
	            	
	            </div>    
            	<div id='area5' class="col-sm-12 connectedSortable">
 
                </div>
            </page>
        <!--</div>

        <div class='statistic-row row'>
                <div id='area5' class="col-sm-12 connectedSortable">
 
                </div>
        </div>-->
    
