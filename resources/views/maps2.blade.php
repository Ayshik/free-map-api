<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-Provider Map with Language Support</title>
    
    <!-- Leaflet CSS (for OpenStreetMap and map handling) -->
    <link href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" rel="stylesheet" />
    <!-- Bootstrap CSS for styling -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        #map {
            height: 600px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="text-center">Multi-Provider Map with Language and Routing</h1>

    <!-- Map Provider and Language Selector -->
    <div class="form-group">
        <label for="provider">Select Map Provider:</label>
        <select id="provider" class="form-control mb-2">
            <option value="osm">OpenStreetMap</option>
            <option value="google">Google Maps</option>
            <option value="tomtom">TomTom Maps</option>
            <option value="opencage">OpenCage</option>
            <option value="geocode">Geocode.xyz</option>
        </select>

        <label for="language">Select Language:</label>
        <select id="language" class="form-control mb-2">
            <option value="en">English</option>
            <option value="bn">Bangla</option>
            <!-- Add more languages if needed -->
        </select>
    </div>

    

    <!-- Input Fields for From, Destination, and Single Place Search -->
    <div class="form-group">
        <input type="text" id="from" class="form-control mb-2" placeholder="Enter 'From' location">
        <input type="text" id="destination" class="form-control mb-2" placeholder="Enter 'Destination' location">
        <button class="btn btn-primary mb-2" onclick="searchRoute()">Get Route</button>
        <button class="btn btn-secondary mb-2" onclick="getCurrentLocation()">Get My Location</button>
        <input type="text" id="searchPlace" class="form-control mb-2" placeholder="Search a place">
        <button class="btn btn-success" onclick="searchPlace()">Search Place</button>
    </div>
<!-- Map Provider and API Information Display -->
    <div id="provider-info" class="alert alert-info mb-2">
        <strong>Current Map Provider:</strong> OpenStreetMap<br>
        <strong>API Information:</strong> 
        <ul>
            <li><strong>OpenStreetMap:</strong> Free Tier: Unlimited free access.</li>
            <li><strong>Google Maps:</strong> Free Tier: $200 monthly credit (approx 28,000 free loads per month).</li>
            <li><strong>TomTom Maps:</strong> Free Tier: 2,500 requests per day.</li>
            <li><strong>OpenCage:</strong> Free Tier: 2,500 requests per day.</li>
            <li><strong>Geocode.xyz:</strong> Free Tier: 1,000 requests per day.</li>
        </ul>
    </div>
    <!-- Map Container -->
    <div id="map"></div>
</div>

<!-- Leaflet JS (for OpenStreetMap) -->
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<!-- Leaflet Routing Machine for OSRM (Routing) -->
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
<!-- jQuery for AJAX Requests -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Bootstrap JS for styling -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- API keys for different providers (use your own keys) -->
<script>
    const OPENCAGE_API_KEY = 'c4395484042e4b6eb2ead116a0d971dd';
    const GEOCODE_XYZ_API_KEY = '995567073128374811366x79388';
    const TOMTOM_MAPS_KEY = 'Zb9vpAIRgCCJSkT09zF6begFyIZTPCmU';
    const GOOGLE_MAPS_KEY = 'hhhhhhhhhhhfdtdhtss';
</script>

<script>
    // Initialize the map with default OpenStreetMap
    var map = L.map('map').setView([23.8103, 90.4125], 13);

    // Layers for different map providers
    var osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    var googleLayer = L.tileLayer(`https://mt1.google.com/vt/lyrs=r&x={x}&y={y}&z={z}&language=en&key=${GOOGLE_MAPS_KEY}`, {
        attribution: '© Google'
    });

    var tomtomLayer = L.tileLayer(`https://api.tomtom.com/map/1/tile/basic/main/{z}/{x}/{y}.png?key=${TOMTOM_MAPS_KEY}&language=en`, {
        attribution: '© TomTom'
    });

    // Add more layers as needed
    var opencageLayer = L.tileLayer(`https://maps.opencagedata.com/v1/tile/{z}/{x}/{y}.png?key=${OPENCAGE_API_KEY}`, {
        attribution: '© OpenCage'
    });

    var geocodeLayer = L.tileLayer(`https://maps.geocode.xyz/{z}/{x}/{y}.png?key=${GEOCODE_XYZ_API_KEY}`, {
        attribution: '© Geocode.xyz'
    });

    // Function to switch map provider
    document.getElementById('provider').addEventListener('change', function () {
        var selectedProvider = this.value;
        switchProvider(selectedProvider);
    });

    function switchProvider(provider) {
        map.eachLayer(function (layer) {
            map.removeLayer(layer);
        });

        switch (provider) {
            case 'osm':
                osmLayer.addTo(map);
                break;
            case 'google':
                googleLayer.addTo(map);
                break;
            case 'tomtom':
                tomtomLayer.addTo(map);
                break;
            case 'opencage':
                opencageLayer.addTo(map);
                break;
            case 'geocode':
                geocodeLayer.addTo(map);
                break;
        }

        // Update provider info
        updateProviderInfo(provider);

        // Set the language
        changeMapLanguage(provider, document.getElementById('language').value);
    }

    // Function to update provider info
    function updateProviderInfo(provider) {
        var providerInfo = {
            'osm': 'OpenStreetMap: Free Tier: Unlimited free access.',
            'google': 'Google Maps: Free Tier: $200 monthly credit (approx 28,000 free loads per month).',
            'tomtom': 'TomTom Maps: Free Tier: 2,500 requests per day.',
            'opencage': 'OpenCage: Free Tier: 2,500 requests per day.',
            'geocode': 'Geocode.xyz: Free Tier: 1,000 requests per day.'
        };
        document.querySelector('#provider-info').innerHTML = `
            <strong>Current Map Provider:</strong> ${providerInfo[provider]}<br>
            <strong>API Information:</strong> 
            <ul>
                <li><strong>OpenStreetMap:</strong> Free Tier: Unlimited free access.</li>
                <li><strong>Google Maps:</strong> Free Tier: $200 monthly credit (approx 28,000 free loads per month).</li>
                <li><strong>TomTom Maps:</strong> Free Tier: 2,500 requests per day.</li>
                <li><strong>OpenCage:</strong> Free Tier: 2,500 requests per day.</li>
                <li><strong>Geocode.xyz:</strong> Free Tier: 1,000 requests per day.</li>
            </ul>
        `;
    }

    // Function to change language for supported maps
    function changeMapLanguage(provider, lang) {
        switch (provider) {
            case 'google':
                googleLayer.setUrl(`https://mt1.google.com/vt/lyrs=r&x={x}&y={y}&z={z}&language=${lang}&key=${GOOGLE_MAPS_KEY}`);
                break;
            case 'tomtom':
                tomtomLayer.setUrl(`https://api.tomtom.com/map/1/tile/basic/main/{z}/{x}/{y}.png?key=${TOMTOM_MAPS_KEY}&language=${lang}`);
                break;
        }
    }

    // Initial call to set the map provider and language
    switchProvider(document.getElementById('provider').value);

    // Function to get route between 'From' and 'Destination'
    function searchRoute() {
        var fromPlace = document.getElementById('from').value;
        var toPlace = document.getElementById('destination').value;

        if (fromPlace && toPlace) {
            $.getJSON(`https://nominatim.openstreetmap.org/search?format=json&q=${fromPlace}`, function (fromData) {
                if (fromData.length > 0) {
                    var fromLat = fromData[0].lat;
                    var fromLon = fromData[0].lon;

                    $.getJSON(`https://nominatim.openstreetmap.org/search?format=json&q=${toPlace}`, function (toData) {
                        if (toData.length > 0) {
                            var toLat = toData[0].lat;
                            var toLon = toData[0].lon;

                            // Remove existing routing control if present
                            if (routingControl) {
                                routingControl.remove();
                            }

                            // Add new routing control
                            routingControl = L.Routing.control({
                                waypoints: [
                                    L.latLng(fromLat, fromLon),
                                    L.latLng(toLat, toLon)
                                ],
                                router: L.Routing.osrmv1({
                                    serviceUrl: 'https://router.project-osrm.org/route/v1'
                                }),
                                geocoder: L.Control.Geocoder.nominatim(),
                                routeWhileDragging: true
                            }).addTo(map);

                            map.setView([fromLat, fromLon], 12);
                        } else {
                            alert('Destination not found.');
                        }
                    });
                } else {
                    alert('From location not found.');
                }
            });
        } else {
            alert('Please enter both "From" and "Destination" locations.');
        }
    }

    // Function to get current location
    function getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var lat = position.coords.latitude;
                var lon = position.coords.longitude;
                map.setView([lat, lon], 13);
                L.marker([lat, lon]).addTo(map)
                    .bindPopup('You are here')
                    .openPopup();
            }, function () {
                alert('Geolocation failed.');
            });
        } else {
            alert('Geolocation is not supported by this browser.');
        }
    }

    // Function to search for a single place
    function searchPlace() {
        var searchPlace = document.getElementById('searchPlace').value;

        if (searchPlace) {
            $.getJSON(`https://nominatim.openstreetmap.org/search?format=json&q=${searchPlace}`, function (data) {
                if (data.length > 0) {
                    var lat = data[0].lat;
                    var lon = data[0].lon;
                    map.setView([lat, lon], 13);
                    L.marker([lat, lon]).addTo(map)
                        .bindPopup(searchPlace)
                        .openPopup();
                } else {
                    alert('Place not found.');
                }
            });
        } else {
            alert('Please enter a place to search.');
        }
    }
</script>

</body>
</html>
