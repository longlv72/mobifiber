@extends('layouts.app')
<style>
    #map {
        height: 100%;
    }

    /*
* Optional: Makes the sample page fill the window.
*/
    html,
    body {
        height: 100%;
        margin: 0;
        padding: 0;
    }
</style>
<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC6kHIBGn3USN4BCLU5zOuCywLYqbQ6bJ8&callback=initMap&v=weekly"
    defer></script>
@section('main_content')
    <div style="height: 82vh">
        <div id="map"></div>
    </div>
    <input type="hidden" id="_token" value="{{ csrf_token() }}">
    <input type="hidden" id="available" value="{{ csrf_token() }}">
    <input type="hidden" id="unavailable" value="{{ csrf_token() }}">
@endsection

@section('scripts')
    <script>
        // The following example creates five accessible and
        // focusable markers.
        function initMap() {
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 6,
                center: {
                    lat: 16.4534687,
                    lng: 107.5359136
                },
            });
            const icon = {
                url: "https://play-lh.googleusercontent.com/rGSQFdxLz8-8lKQXZuzWDTs1fcA6QbnRXSik0cJFGP2chv9vV6nI-UPngsg-io9NIw", // url
                scaledSize: new google.maps.Size(30, 30), // scaled size
                origin: new google.maps.Point(0, 0), // origin
                anchor: new google.maps.Point(0, 0) // anchor
            };
            $.ajax({
                url: '/list-buildings-map',
                type: 'get',
                dataType: 'json',
                success: function(res) {
                    const tourStops = res.data;
                    // Create an info window to share between markers.
                    const infoWindow = new google.maps.InfoWindow();

                    // Create the markers.
                    tourStops.forEach(([position, icon_link, title], i) => {
                        url = icon_link;
                        var _icon = { ...icon, url};
                        debugger

                        const marker = new google.maps.Marker({
                            position,
                            map,
                            title: `${title}`,
                            label: ``,
                            icon: _icon,
                            optimized: true,
                        });
                        // Add a click listener for each marker, and set up the info window.
                        marker.addListener("click", () => {
                            infoWindow.close();
                            infoWindow.setContent(marker.getTitle());
                            infoWindow.open(marker.getMap(), marker);
                        });
                    });
                }
            });

        }
        window.initMap = initMap;
    </script>
@endsection
