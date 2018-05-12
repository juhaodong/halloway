<?php

$username=$_GET['id'];
if($username!=null){
    if(!file_exists("../Users/".$username)){
        mkdir("../Users/".$username,0777,true) or die("../Users/".$username);
    }
    $myfile = fopen("../Users/".$username."/info.txt", "w") or die("../Users/".$username."/info.txt");
    $id=$_GET['name'];

    fwrite($myfile,$id."\n");
    fclose($myfile);

    echo $id;
}
