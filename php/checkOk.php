<?php
/*$createrId=$_GET['createrId'];
deldir("../Users/o4JD05UDAKx5JmuS0SNQwl8goWX8");

return;
function deldir($dir) {
    //先删除目录下的文件：
    $dh=opendir($dir);

    while ($file=readdir($dh)) {
        if($file!="." && $file!="..") {
            $fullpath=$dir."/".$file;
            if(!is_dir($fullpath)) {
                unlink($fullpath);
            } else {
                deldir($fullpath);
            }
        }
    }

    closedir($dh);
    //删除当前文件夹：
    if(rmdir($dir)) {
        return true;
    } else {
        return false;
    }
}*/

header("Access-Control-Allow-Origin: *");
header('Content-type: application/json');
$id=$_GET['id'];
$userId=$_GET['user'];
$createrId=$_GET['createrId'];

$orderPath="../Orders/";

$username=$userId;
$orderPath=$orderPath.$id;
function getMemberCount($id){
    $file=fopen("../Orders/".$id."/member.txt","r");
    $user=0;
    while (!feof($file)){
        $tmp=fgets($file);
        //echo $tmp;
        $tmp=substr($tmp,0,-1);


        if($tmp!=""){

            $user++;
        }

    }
   // echo $user;
    return $user;


}
function getMemberLimit($id){
    $file=fopen("../Orders/".$id."/".$id.".txt","r");
    $user=0;
    while (!feof($file)){
        $tmp=fgets($file);
        //echo $tmp;
        $tmp=substr($tmp,0,-1);
        $user++;
        if($user==12){

            $tmp=explode(":",$tmp)[1];
          //  echo $tmp;
            return $tmp;
        }


    }
    return -1;
}

if(getMemberCount($id)>=getMemberLimit($id)){
    echo "已经超过参与人数上限。";
    return;
}
removeLine($id,"../Users/".$username."/checking.txt");
//save in the total order Path
removeLine($username.",".$id,"../Users/".$createrId."/wantIn.txt");


if($_GET['q']==0){
    return;
}

$member=fopen($orderPath."/member.txt",'a') or die($orderPath."/member.txt");
fwrite($member,$username."\n");
if(!file_exists("../Users/".$username)){
    mkdir("../Users/".$username,0777,true) or die(2);
}


$myfile = fopen("../Users/".$username."/join.txt", "a") or die( 0);
fwrite($myfile,$id."\n");
echo "finish";

    /*$member=fopen($orderPath."/watch.txt",'a') or die($orderPath."/watch.txt");
    fwrite($member,$username."\n");*/

function removeLine($target,$filename){

    $str="";

    $myfile = fopen($filename, "r") or die("cant open file".$filename);
    while (!feof($myfile)){
        $tmp=fgets($myfile);
        if($target==substr($tmp,0,-1)){
            continue;
        }
        $str.=$tmp."\n";

    }
    fclose($myfile);
    $myfile = fopen($filename, "w") or die("cant open file".$filename);
    fwrite($myfile,$str."\n");

}


return;

