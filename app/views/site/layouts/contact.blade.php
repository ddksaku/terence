@extends('site.layouts.default')

@section('bottom_scripts')
    @parent
    
    @if($contact->contact_map_status)
    <div id="google_map_address" style="display: none;">{{ str_replace("\n", '<br>', $contact->contact_address) }}</div>
        <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
        <script type="text/javascript">
        jQuery(document).ready(function(){
                var myLatlng = new google.maps.LatLng({{ $contact->contact_lat_value }}, {{ $contact->contact_lon_value }});
                var myOptions = {
                  center:myLatlng,
                  zoom: {{ $contact->contact_zoom_value }},
                  mapTypeId: google.maps.MapTypeId.{{ $contact->contact_type_value }},
                  scrollwheel: false
                };
                var map = new google.maps.Map(document.getElementById("map"),  myOptions);
                var marker = new google.maps.Marker({
                  position: myLatlng,
                  map: map,
                  title:"Click Me for more info!"
                });

                var infowindow = new google.maps.InfoWindow({});

                google.maps.event.addListener(marker, 'click', function() {
                        infowindow.setContent(jQuery('#google_map_address').html()); //sets the content of your global infowindow to string "Tests: "
                        infowindow.open(map,marker); //then opens the infowindow at the marker
                });
                marker.setMap(map);
        })
        </script>
     @endif
@stop