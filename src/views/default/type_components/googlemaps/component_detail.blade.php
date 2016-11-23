<div id="map"></div>
							
<script type="text/javascript">
function initMap{{$index}}() {	
	var map = new google.maps.Map(document.getElementById('map'), {
	  @if($row->{$form['latitude']} && $row->{$form['longitude']})
	  	center: {lat: <?php echo $row->{$form['latitude']};?>, lng: <?php echo $row->{$form['longitude']};?> },
	  @else 
	  	center: {lat: -7.0157404, lng: 110.4171283},
	  @endif
	  zoom: 12
	});

	var marker = new google.maps.Marker({
	  position: {lat: <?php echo $row->{$form['latitude']};?>, lng: <?php echo $row->{$form['latitude']};?> },
	  map: map,					          
	  title: 'Location Here !'
	});										    										    
}
$(function() {
	initMap{{$index}}();
})
</script>		        					    
