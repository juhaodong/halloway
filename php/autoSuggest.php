<?php
$baseURL="http://dev.virtualearth.net/REST/v1/Locations/DE/";
$country = "US";
$key="AuA6fw00cOgbJ0X0Ym6E6SdtKFA6ME9Q8UTnoy4odVwPAogGl-T31yNcDkN63Zp7";
$addressLine = str_ireplace(" ","%20",$_GET['address']);

$revGeocodeURL = $baseURL."/".$addressLine."?output=json&key=".$key;
$rgOutput = file_get_contents($revGeocodeURL);

echo $rgOutput;
