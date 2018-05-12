<?php
$id=$_GET['id'];
$cid=$_GET['userId'];
$orderpath="../Orders/".$id."/";
$eventfile=$orderpath.$id.".txt";
$file=fopen($eventfile,"r") or die($eventfile);
$arr=array();
while(!feof($file)){
   $str= fgets($file);

   $tmpArr=explode(":",$str,2);
   $tmpArr[1]=substr($tmpArr[1],0,-1);
   if($tmpArr[1]==""||$tmpArr[1]=="undefined"){
       $tmpArr[1]="unset";
   }
   if($tmpArr[0]==""){
       continue;
   }
   $arr[$tmpArr[0]]=$tmpArr[1];

}
$file=fopen($orderpath."member.txt","r");
    $user=0;
$arr['isMember']=false;

$arr['Members']=[];
while (!feof($file)){
    $tmp=fgets($file);
    //echo $tmp;
    $tmp=substr($tmp,0,-1);

    if($tmp==$cid){
        $arr['isMember']=true;
    }
    if($tmp!=""){
        array_push($arr['Members'],userIdtoName($tmp));
        $user++;
    }

}
$arr["personCount"]=$user;
echo json_encode($arr);
function userIdtoName($id){
    if(file_exists("../Users/".$id."/info.txt")){
        $mFile=fopen("../Users/".$id."/info.txt","r");
        $name=substr( fgets($mFile),0,-1);
        return $name;
    }
    return "未注册用户: ".$id;

}