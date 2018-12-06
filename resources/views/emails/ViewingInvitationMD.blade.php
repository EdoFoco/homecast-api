@component('mail::message')
# {{$presenter->name}} has invited you to join a live viewing
<p class='sub'>
    ** Live Viewings require Homecast app to be installed on your device. <br />Download it <a href="google.com">here</a> and join us!
</p>
@component('mail::panel')
    <div class="details-wrapper">
        <div class='panel-title'>{{Carbon\Carbon::parse($viewing->date_time)->format('d M Y')}}<br />{{Carbon\Carbon::parse($viewing->date_time)->format('g:i A')}} (UK time)</div>
        <p class="section-title">Address</p>
        <p class="section-description">{{$viewing->property->address}}</p>
        <p class="section-title">Rooms</p>
        <div class="rooms-container">
            <div class="room">{{$viewing->property->bedrooms}} bed(s)</div>
            <div class="room">{{$viewing->property->bathrooms}} bathroom(s)</div>
            <div class="room">{{$viewing->property->living_rooms}} living room(s)</div>
        </div>
        <p class="section-title">Price</p>
        <p class="section-description">{{$viewing->property->price}} £/m</p>
        <p class="section-title">Description</p>
        <p class="section-description">{{$viewing->property->description}}</p>
        <div class="cta-container">
            @component('mail::button', ['url' => 'http://127.0.0.1:8111/api/redirect?resources=properties,viewings&identifiers='.$viewing->property->id.','.$viewing->id ])
                Go to Viewing
            @endcomponent
        </div>
       
        
        <img class="property-image" src="{{$viewing->property->images[0]->url}}" />
    </div>
    
  
@endcomponent



<!-- homecastapp://guest/properties/{{$viewing->property->id}}/viewings/{{$viewing->id}} -->

<br />
<br />

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent