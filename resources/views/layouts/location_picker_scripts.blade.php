
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD36Lt_W4pJk7v9NHP_r_WsV1AG3awtqRE&callback=initialize&sensor=false&libraries=places"
  type="text/javascript"></script>
  


<script>
//    google.maps.event.addDomListener(window, 'load', initialize);

   function initialize() {
    let locationField = document.getElementById('autocomplete');
    var geocoder = new google.maps.Geocoder();

    let autocomplete = new google.maps.places.Autocomplete(locationField);
    google.maps.event.addListener(autocomplete, 'place_changed', function () {
        
        specifiedLocationObject = place;
        
        var place = autocomplete.getPlace();
           $('#latitude').val(place.geometry['location'].lat());
           $('#logitude').val(place.geometry['location'].lng());
           var addressChunks = place.formatted_address.split(',');
           console.log(place.formatted_address);
           console.log(addressChunks[addressChunks.length - 1]);
           console.log(addressChunks[addressChunks.length - 2]);
            var city = "", country = "";
           if(addressChunks.length > 2){
                country = addressChunks[addressChunks.length - 1];
                city = addressChunks[addressChunks.length - 2];
           }else if(addressChunks.length > 1){
                country = addressChunks[addressChunks.length - 1];
                city = addressChunks[addressChunks.length - 2];
           }
           $('#country').val(country);
           $('#city').val(city);
           $('#address').val(place.formatted_address);
           $("#lat_area").removeClass("d-none");
           $("#long_area").removeClass("d-none");
    });
       
   }


  
</script>
<script type="text/javascript">
  $('#addLocation').on('click',function(event){
        event.preventDefault();
        $("#location_modal").modal('show');
  });
    $('#location_form').on('submit',function(event){
        event.preventDefault();

        let country = $('#country').val();
        let city = $('#city').val();
        let address = $('#address').val();
        let latitude = $('#latitude').val();
        let longitude = $('#longitude').val();

        $.ajax({
          url: "{{ route("admin.locations.store") }}",
          type:"POST",
          data:{
            "_token": "{{ csrf_token() }}",
            country:country,
            city:city,
            address:address,
            latitude:latitude,
            logitude:longitude,
          },
          success:function(response){
            console.log(response);
          },
         });
        });
      </script>
