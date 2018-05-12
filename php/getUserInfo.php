<?php

$username=$_GET['id'];
$myfile = fopen("../Users/".$username."/info.txt", "r") or die( 0);
$name=fgets($myfile);
echo $name;