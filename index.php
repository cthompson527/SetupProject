<?php
/**
 * Created by PhpStorm.
 * User: corythompson
 * Date: 9/28/16
 * Time: 3:53 PM
 */
require_once ('lib/YelpClient.php');

$geo = [];
$businesses = [];
if (isset($_GET["city"])) {
    $city = $_GET["city"];

    if (isset($_GET["state"])) $state = $_GET["state"];
    getGeographicLocation($city);
}

if (isset($_GET["submitFood"])) {
    $category = $_GET["food"];
//    print_r($category);
    getLocalRestaurants($category);
}

function getGeographicLocation($city) {
    $key = 'AIzaSyClSTs0ENWZXyWl3gjiukdsG9GBAtIUCWM';
    $googleSite = 'https://maps.googleapis.com/maps/api/geocode/json?';

    $returnValue = file_get_contents($googleSite . 'address=' . $city . '&key=' . $key);
    $jsonValue = json_decode($returnValue, true);
//    print_r($jsonValue['results'][0]['geometry']['location']['lat']);
    $GLOBALS['geo']['lat'] = $jsonValue['results'][0]['geometry']['location']['lat'];
    $GLOBALS['geo']['lng'] = $jsonValue['results'][0]['geometry']['location']['lng'];
//    print_r($GLOBALS['geo']['lat']);
}

function getLocalRestaurants($category) {
    $returnValue = search($category, $GLOBALS['geo'], 10);          //FIXME: Not dynamic on place. Always searching BCS
//    print_r($returnValue);
    $jsonValue = json_decode($returnValue, true);
//    setMapPoints();
    foreach ($jsonValue['businesses'] as $business) {
        $GLOBALS['businesses'][] = array(
            'name' => $business['name'],
            'rating' => $business['rating'],
            'image_url' => $business['image_url'],
            'phone' => $business['display_phone'],
            'location' => array(
                'lat' => $business['location']['coordinate']['latitude'],
                'lng' => $business['location']['coordinate']['longitude']
            )
        );
    }
//    print_r($businesses);
}
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
            height: 60%;
            width: 70%;
            /*margin: auto;*/
        }
    </style>
    <link rel="stylesheet" type="text/css" href="styles/style.css">
    <script src="vars.js"></script>
    <script>
        var map;
        function initMap() {
            // loop taken from StackOverflow: http://stackoverflow.com/a/12813649
            var geo = {};
            <?php foreach($geo as $key => $value) { ?>
                geo.<?php echo $key; ?> = <?php echo $value; ?>;
            <?php } ?>
            if (Object.keys(geo).length == 0) {
                geo.lat = 30.628;
                geo.lng = -96.334;
            }
            map = new google.maps.Map(document.getElementById('map'), {
                center: {lat: geo.lat, lng: geo.lng},
                zoom: 10
            });

            if (<?php echo isset($businesses) ?>) {
                setMap();
            }
        }

        function setMap() {
            <?php foreach ($businesses as $business) { ?>
                var businessName = "<?php echo $business['name'] ?>";
                var businessRating = "<?php echo $business['rating'] ?>";
                var businessPhone = "<?php echo $business['phone'] ?>";
                var businessImageURL = "<?php echo $business['image_url'] ?>";
                var businessLocation = { lat: <?php echo $business['location']['lat'] ?>,
                                         lng: <?php echo $business['location']['lng'] ?> };
                var marker = new google.maps.Marker({
                    position: businessLocation,
                    map: map,
                    title: businessName
                });
            <?php } ?>
        }
    </script>
</head>
<body>

<div id="sidebar">
    <form method="get" action="<?= $_SERVER['PHP_SELF']; ?>">
        <p>
            <label for="city">City:</label>
            <input type="text" name="city" id="city">
        </p>
        <p>
            <label for="state">State:</label>
            <input type="text" name="state" id="state">
        </p>
        <p><input type="submit" name="submitLocation" value="Search for City"></p>
    </form>

    <form method="get" action="<?= $_SERVER['PHP_SELF']; ?>">
        <p><input type="radio" name="food" value="candy">Candy Stores</p>
        <p><input type="radio" name="food" value="cheese">Cheese Shops</p>
        <p><input type="radio" name="food" value="chocolate">Chocolatiers</p>
        <p><input type="radio" name="food" value="dagashi">Dagashi</p>
        <p><input type="radio" name="food" value="frozenfood">Frozen Food</p>
        <p><input type="radio" name="food" value="markets">Fruits & Veggies</p>
        <p><input type="radio" name="food" value="healthmarkets">Health Markets</p>
        <p><input type="radio" name="food" value="herbsandspices">Herbs & Spices</p>
        <p><input type="radio" name="food" value="macarons">Macarons</p>
        <p><input type="radio" name="food" value="meats">Meat Shops</p>
        <p><input type="radio" name="food" value="oliveoil">Olive Oil</p>
        <p><input type="radio" name="food" value="pastashops">Pasta Shops</p>
        <p><input type="radio" name="food" value="popcorn">Popcorn Shops</p>
        <p><input type="radio" name="food" value="seafoodmarkets">Seafood Markets</p>
        <p><input type="radio" name="food" value="tofu">Tofu Shops</p>
        <p><input type="submit" name="submitFood" value="Search for Shops"></p>
    </form>
</div>
<div id="map"></div>


<script type="text/javascript">
    var maps = "https://maps.googleapis.com/maps/api/js?key=" + googleKey + "&callback=initMap";
    document.write("<script async defer type='text/javascript' src='"+ maps + "'><\/script>");
</script>
</body>
</html>


