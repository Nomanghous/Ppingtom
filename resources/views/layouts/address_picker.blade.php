<script type="text/javascript">
    google.maps.event.addDomListener(window, 'load', function () {
        var places = new google.maps.places.Autocomplete(document.getElementById('txtPlaces'));
        google.maps.event.addListener(places, 'place_changed', function () {

        });
    });
</script>
<span>Location:</span>
<input type="text" id="txtPlaces" style="width: 250px" placeholder="Enter a location" />
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&key=" {{ env('GOOGLE_MAPS_API_KEY') }}></script>
