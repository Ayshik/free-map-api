<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Map APIs in Laravel</title>

    <!-- Bootstrap CSS from CDN -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Leaflet CSS (for OpenStreetMap) -->
    <link href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" rel="stylesheet" />

    <style>
        #osmMap, #opencage, #geocodexyz, #googleMap {
            height: 400px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center mb-5">Map APIs with Free Tiers</h1>

    <!-- OpenStreetMap via Leaflet -->
    <div class="card mb-4">
        <div class="card-header">OpenStreetMap (Free and unlimited usage)</div>
        <div class="card-body">
            <div id="osmMap"></div>
        </div>
    </div>

    <!-- OpenCage Geocoder -->
    <div class="card mb-4">
        <div class="card-header">OpenCage Geocoder (2,500 requests/day)</div>
        <div class="card-body">
            <div id="opencage"></div>
        </div>
    </div>

    <!-- Geocode.xyz -->
    <div class="card mb-4">
        <div class="card-header">Geocode.xyz (10,000 requests/day)</div>
        <div class="card-body">
            <div id="geocodexyz"></div>
        </div>
    </div>

    <!-- Google Maps -->
    <div class="card mb-4">
        <div class="card-header">Google Maps</div>
        <div class="card-body">
            <div id="googleMap"></div>
        </div>
    </div>
</div>

<!-- Bootstrap JS, Popper.js, and jQuery from CDN -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Leaflet JS (for OpenStreetMap) -->
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

<!-- Google Maps JS API -->
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY"></script>

<!-- Update this section in your existing code -->
<script>
    // Fetch API keys from Laravel's env
    const openCageApiKey = '{{ env('OPENCAGE_API_KEY') }}';
    const geocodeXyzApiKey = '{{ env('GEOCODE_XYZ_API_KEY') }}';
    const GOOGLE_MAPS_KEY = '{{ env('GOOGLE_API_KEY') }}'; 

    // Initialize OpenStreetMap using Leaflet
    var osmMap = L.map('osmMap').setView([23.8103, 90.4125], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(osmMap);

    // OpenCage Geocoder Initialization
    fetch(`https://api.opencagedata.com/geocode/v1/json?q=Dhaka&key=${openCageApiKey}`)
        .then(response => response.json())
        .then(data => {
            if (data.results && data.results.length > 0) {
                var lat = data.results[0].geometry.lat;
                var lng = data.results[0].geometry.lng;
                var openCageMap = L.map('opencage').setView([lat, lng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(openCageMap);
            }
        });

    // Geocode.xyz Initialization
    fetch(`https://geocode.xyz/Dhaka?json=1&auth=${geocodeXyzApiKey}`)
        .then(response => response.json())
        .then(data => {
            if (data.latt && data.longt) {
                var geocodeXyzMap = L.map('geocodexyz').setView([data.latt, data.longt], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(geocodeXyzMap);
            }
        });

    // Initialize Google Maps with custom tile layer
    var googleMap = L.map('googleMap').setView([23.8103, 90.4125], 13);
    var googleLayer = L.tileLayer(`https://mt1.google.com/vt/lyrs=r&x={x}&y={y}&z={z}&language=en&key=${GOOGLE_MAPS_KEY}`, {
        attribution: '© Google'
    }).addTo(googleMap);
</script>


</body>
</html>
