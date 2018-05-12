<?php
header("Access-Control-Allow-Origin: *");
header('Content-type: application/json');
$id=$_GET['id'];

$userId=$_GET['userId'];


$filename="/creat.txt";
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
}
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
function getAllMember($id){
    $myfile = fopen("../Orders/".$id."/member.txt", "r") or die("../Orders/".$id."/member.txt");
    $str="";
    while (!feof($myfile)){
        $tmp=fgets($myfile);
        if(strcmp($tmp,"\n")==0){
            continue;
        }
        $str.=substr($tmp,0,-1).",";

    }
    return $str;
}
$member=explode(",",getAllMember($id));
foreach ($member as $value){
    if(strcmp($value,"")!=0){
        if($value!=$userId){
            removeLine($id,"../Users/".$value."/join.txt");
        }

        if(file_exists("../Users/".$value."/checking.txt")){
            removeLine($id,"../Users/".$value."/checking.txt");
        }
        if(file_exists("../Users/".$userId."/wantIn.txt")){
            removeLine($value.",".$id,"../Users/".$userId."/wantIn.txt");
        }

    }
}

echo json_encode($member);

deldir("../Orders/".$id);
removeLine($id,"../Users/".$userId.$filename);




echo 'good';