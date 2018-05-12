<?php

$longitude=$_GET['lo'];//用户当前定位的经度

$latitude=$_GET['la'];//用户当前定位的纬度

$baseURL="http://dev.virtualearth.net/REST/v1/Locations";
$point = $latitude.",".$longitude;

$key="AuA6fw00cOgbJ0X0Ym6E6SdtKFA6ME9Q8UTnoy4odVwPAogGl-T31yNcDkN63Zp7";
$revGeocodeURL = $baseURL."/".$point."?output=json&key=".$key;
//echo $revGeocodeURL;
$rgOutput = file_get_contents($revGeocodeURL);

echo $rgOutput;





/*$place_url='https://maps.googleapis.com/maps/api/geocode/json??latLng='.$latitude.','.$longitude.'&key=AIzaSyBIkKAd0S2KirZNraqalVd8QfYf_SkRyms';
$json_place=file_get_contents($place_url);
$place_arr=json_decode($json_place,true);
print_r($json_place) ;
$address=$place_arr['result']['formatted_address'];
echo $address;*/
?>
