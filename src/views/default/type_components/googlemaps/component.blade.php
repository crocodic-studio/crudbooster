
                		<div class='form-group peta {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}'>
                			<label class='control-label col-sm-2'>{{$form['label']}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>
                			
                			<div class="{{$col_width?:'col-sm-10'}}">
							<input id="pac-input" class="controls" autofocus type="text"
						        placeholder="Enter a location">
						    <div id="type-selector" class="controls">
						      <input type="radio" name="type" id="changetype-all" checked="checked">
						      <label for="changetype-all">All</label>

						      <input type="radio" name="type" id="changetype-establishment">
						      <label for="changetype-establishment">Establishments</label>

						      <input type="radio" name="type" id="changetype-address">
						      <label for="changetype-address">Addresses</label>

						      <input type="radio" name="type" id="changetype-geocode">
						      <label for="changetype-geocode">Geocodes</label>
						    </div>
						    <div id="map"></div>

						    </div>
						</div>
                		<script type="text/javascript">
                		  var geocoder;
					      function initMap{{$index}}() {
					      	geocoder = new google.maps.Geocoder();
					        var map = new google.maps.Map(document.getElementById('map'), {
					          @if($row->{$form['latitude']} && $row->{$form['longitude']})
							  	center: {lat: <?php echo $row->{$form['latitude']};?>, lng: <?php echo $row->{$form['longitude']};?> },
							  @else 
							  	center: {lat: -7.0157404, lng: 110.4171283},
							  @endif
					          zoom: 12
					        });
					        
					        var marker = new google.maps.Marker({
					          position: {lat: <?php echo $row->{$form['latitude']}?>, lng: <?php echo $row->{$form['longitude']}?> },
					          map: map,
					          draggable:true,
					          title: 'Location Here !'
					        });
					       

					        var input = /** @type  {!HTMLInputElement} */(
					            document.getElementById('pac-input'));

					        var types = document.getElementById('type-selector');
					        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
					        map.controls[google.maps.ControlPosition.TOP_LEFT].push(types);

					        var autocomplete = new google.maps.places.Autocomplete(input);
					        autocomplete.bindTo('bounds', map);

					        var infowindow = new google.maps.InfoWindow();
					        var marker = new google.maps.Marker({
					          map: map,
					          draggable:true,
					          anchorPoint: new google.maps.Point(0, -29)
					        });

					        google.maps.event.addListener(marker, 'dragend', function(marker){
					        	

					        	  geocoder.geocode({
								    latLng: marker.latLng
								  }, function(responses) {
								    if (responses && responses.length > 0) {
								      address = responses[0].formatted_address;
								    } else {
								      address = 'Cannot determine address at this location.';
								    }

								    @if($form['googlemaps_address'])
								  		$("input[name={{$form['address']}}]").val(address);
									@endif

									console.log(address);

								    infowindow.setContent(address);
								    
								  });

						        var latLng = marker.latLng; 
						        latitude = latLng.lat();
						        longitude = latLng.lng();
						        						          
						        $("input[name={{$form['latitude']}}]").val(latitude);
						        $("input[name={{$form['longitude']}}]").val(longitude);								          						          	
						     });

					        autocomplete.addListener('place_changed', function() {
					          infowindow.close();
					          marker.setVisible(false);
					          var place = autocomplete.getPlace();
					          if (!place.geometry) {
					            window.alert("Autocomplete's returned place contains no geometry");
					            return;
					          }
					      
					          if (place.geometry.viewport) {
					            map.fitBounds(place.geometry.viewport);
					          } else {
					            map.setCenter(place.geometry.location);
					            map.setZoom(17); 
					          }
			
					          marker.setPosition(place.geometry.location);
					          marker.setVisible(true);

					          var address = '';
					          if (place.address_components) {
					            address = [
					              (place.address_components[0] && place.address_components[0].short_name || ''),
					              (place.address_components[1] && place.address_components[1].short_name || ''),
					              (place.address_components[2] && place.address_components[2].short_name || '')
					            ].join(' ');
					          }

					          var latitude = place.geometry.location.lat();
							  var longitude = place.geometry.location.lng(); 

							  @if($form['googlemaps_address'])
							  	$("input[name={{$form['address']}}]").val(address);
							  @endif
					          
					          $("input[name={{$form['latitude']}}]").val(latitude);
						      $("input[name={{$form['longitude']}}]").val(longitude);

					          infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
					          infowindow.open(map, marker);
					        });

					        function setupClickListener(id, types) {
					          var radioButton = document.getElementById(id);
					          radioButton.addEventListener('click', function() {
					            autocomplete.setTypes(types);
					          });
					        }

					        setupClickListener('changetype-all', []);
					        setupClickListener('changetype-address', ['address']);
					        setupClickListener('changetype-establishment', ['establishment']);
					        setupClickListener('changetype-geocode', ['geocode']);
					      }
					      $(function() {
					      	initMap{{$index}}();
					      })
					    </script>		        					    