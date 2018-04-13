@if($form['latitude'] && $form['longitude'])
    <a href='javascript:void(0)' onclick='showModalMap{{$name}}()' title="Click to view the map">
        <i class='fa fa-map-marker'></i> {{$value}}
    </a>
@else
    {{$value}}
@endif


<div id='googlemaps-modal-{{$name}}' class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i class='fa fa-search'></i> View Map</h4>
            </div>
            <div class="modal-body">

                <div class="map" id='map-{{$name}}'></div>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


@push('bottom')
    <script type="text/javascript">
        function showModalMap{{$name}}() {
            $('#googlemaps-modal-{{$name}}').modal('show');
        }

        var is_init_map_{{$name}} = false;
        $('#googlemaps-modal-{{$name}}').on('shown.bs.modal', function () {
            if (is_init_map_{{$name}} == false) {
                initMap{{$name}}();
                is_init_map_{{$name}} = true;
            }
        });

        function initMap{{$name}}() {
                    @if($row->{$form['latitude']} && $row->{$form['longitude']})
            var map = new google.maps.Map(document.getElementById('map-{{$name}}'), {
                    center: {lat: <?php echo $row->{$form['latitude']} ?: 0;?>, lng: <?php echo $row->{$form['longitude']} ?: 0;?> },
                    zoom: 12
                });
            var infoWindow = new google.maps.InfoWindow();

            var marker = new google.maps.Marker({
                position: {lat: <?php echo $row->{$form['latitude']} ?: 0;?>, lng: <?php echo $row->{$form['longitude']} ?: 0;?> },
                map: map,
                title: '{{$value}}'
            });

            infoWindow.close();
            infoWindow.setContent("{{$value}}");
            infoWindow.open(map, marker);
            @else
            $('#googlemaps-modal-{{$name}} .modal-body').html("<div align='center'>Sorry the map is not found !</div>");
            @endif
        }
    </script>
@endpush        					    
