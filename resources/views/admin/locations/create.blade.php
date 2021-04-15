@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.location.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.locations.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.location.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.location.fields.name_helper') }}</span>
            </div>
            
            <div class="form-group">
            <div class="form-group">
                <label for="autocomplete"> Location/City/Address </label>
                <input type="text" name="autocomplete" id="autocomplete" class="form-control" placeholder="Select Location">
            </div>
            </div>

            <div class="form-group">
                <label class="required" for="city">{{ trans('cruds.location.fields.city') }}</label>
                <input class="form-control {{ $errors->has('city') ? 'is-invalid' : '' }}" type="text" name="city" id="city" value="{{ old('city', '') }}" required>
                @if($errors->has('city'))
                    <div class="invalid-feedback">
                        {{ $errors->first('city') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.location.fields.city_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="address">{{ trans('cruds.location.fields.address') }}</label>
                <input class="form-control {{ $errors->has('address') ? 'is-invalid' : '' }}" type="text" name="address" id="address" value="{{ old('address', '') }}" required>
                @if($errors->has('address'))
                    <div class="invalid-feedback">
                        {{ $errors->first('address') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.location.fields.address_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="country">{{ trans('cruds.location.fields.country') }}</label>
                <input class="form-control {{ $errors->has('country') ? 'is-invalid' : '' }}" type="text" name="country" id="country" value="{{ old('country', '') }}" required>
                @if($errors->has('country'))
                    <div class="invalid-feedback">
                        {{ $errors->first('country') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.location.fields.country_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="zip_code">{{ trans('cruds.location.fields.zip_code') }}</label>
                <input class="form-control {{ $errors->has('zip_code') ? 'is-invalid' : '' }}" type="text" name="zip_code" id="zip_code" value="{{ old('zip_code', '') }}">
                @if($errors->has('zip_code'))
                    <div class="invalid-feedback">
                        {{ $errors->first('zip_code') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.location.fields.zip_code_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="latitude">{{ trans('cruds.location.fields.latitude') }}</label>
                <input class="form-control {{ $errors->has('latitude') ? 'is-invalid' : '' }}" type="number" name="latitude" id="latitude" value="{{ old('latitude', '') }}" step="0.000000001" required>
                @if($errors->has('latitude'))
                    <div class="invalid-feedback">
                        {{ $errors->first('latitude') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.location.fields.latitude_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="logitude">{{ trans('cruds.location.fields.logitude') }}</label>
                <input class="form-control {{ $errors->has('logitude') ? 'is-invalid' : '' }}" type="number" name="logitude" id="logitude" value="{{ old('logitude', '') }}" step="0.000000001" required>
                @if($errors->has('logitude'))
                    <div class="invalid-feedback">
                        {{ $errors->first('logitude') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.location.fields.logitude_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="photo">{{ trans('cruds.productCategory.fields.photo') }}</label>
                <div class="needsclick dropzone {{ $errors->has('photo') ? 'is-invalid' : '' }}" id="photo-dropzone">
                </div>
                @if($errors->has('photo'))
                    <div class="invalid-feedback">
                        {{ $errors->first('photo') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.productCategory.fields.photo_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection
@section('scripts')
<script>
    Dropzone.options.photoDropzone = {
    url: '{{ route('admin.locations.storeMedia') }}',
    maxFilesize: 2, // MB
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
    maxFiles: 1,
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 2,
      width: 4096,
      height: 4096
    },
    success: function (file, response) {
      $('form').find('input[name="photo"]').remove()
      $('form').append('<input type="hidden" name="photo" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="photo"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
        @if(isset($productCategory) && $productCategory->photo)
            var file = {!! json_encode($productCategory->photo) !!}
                this.options.addedfile.call(this, file)
            this.options.thumbnail.call(this, file, file.preview)
            file.previewElement.classList.add('dz-complete')
            $('form').append('<input type="hidden" name="photo" value="' + file.file_name + '">')
            this.options.maxFiles = this.options.maxFiles - 1
        @endif
    },
    error: function (file, response) {
        if ($.type(response) === 'string') {
            var message = response //dropzone sends it's own error messages in string
        } else {
            var message = response.errors.file
        }
        file.previewElement.classList.add('dz-error')
        _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
        _results = []
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            node = _ref[_i]
            _results.push(node.textContent = message)
        }

        return _results
    }
}
</script>
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
@endsection