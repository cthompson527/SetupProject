<?php
/**
 * Created by PhpStorm.
 * User: corythompson
 * Date: 9/28/16
 * Time: 3:53 PM
 */
?>


<!DOCTYPE html>
<html>
<head>
    <title>Simple Map</title>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        #map {
            height: 100%;
        }
    </style>
    <script src="vars.js"></script>
</head>
<body>
<div id="map"></div>
<script>
    var map;
    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: -34.397, lng: 150.644},
            zoom: 8
        });
    }
</script>
<script type="text/javascript">
    var maps = "https://maps.googleapis.com/maps/api/js?key=" + googleKey + "&callback=initMap"
    document.write("<script type='text/javascript' src='"+ maps + "'><\/script>");
</script>
</body>
</html>
