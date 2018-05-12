<?php
header("Access-Control-Allow-Origin: *");
header('Content-type: application/json');
$id=$_GET['id'];
$userId=$_GET['userId'];
$myfile = fopen("../Users/".$userId."/join.txt", "r") or die("创建者不可以退出活动");
$info=fopen("../Orders/".$id."/member.txt","r") or die("../Orders/".$id."/member.txt");
$str="";
while (!feof($myfile)){

    $tmp=fgets($myfile);

    if($tmp=="\n"){
        continue;
    }
    if($id!=substr($tmp,0,-1)){
        $str.=$tmp."\n";
    }


}

fclose($myfile);
$myfile = fopen("../Users/".$userId."/join.txt", "w") or die("../Users/".$userId."/join.txt" );
fwrite($myfile,$str."\n");


$str="";
while (!feof($info)){
    $tmp=fgets($info);
    if($tmp=="\n"){
        continue;
    }
    if($userId!=substr($tmp,0,-1)){

        $str.=$tmp."\n";
    }

}
//echo "New INFO".$str;
fclose($info);
$info=fopen("../Orders/".$id."/member.txt","w") or die("../Orders/".$id."/member.txt");
fwrite($info,$str);
echo error_get_last()['message'];
echo 'good';