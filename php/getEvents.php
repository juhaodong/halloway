<?php

$userId=$_GET['userId'];

$userPath="../Users/".$userId."/";
$userFiles=array("creat.txt","join.txt","checking.txt","wantIn.txt");
$result=array();
foreach ($userFiles as  $value){


    $tmp=array();
    if(file_exists($userPath.$value)){
        $tmpFile=fopen($userPath.$value,"r") or die("\nunable to open File.".$userPath.$value);
        while(!feof($tmpFile)){
            $tmpId=substr(fgets($tmpFile),0,-1);
            $inId=explode(",",$tmpId);
            if($inId[1]!=null){
                $tmpId=$inId[1];
                $inId=$inId[0];
            }
            if($tmpId!=""){
                $event=getSingleEvent($tmpId);
                $event['id']=$tmpId;
                $event['inUser']=$inId;
                array_push($tmp,$event);
            }

        }
    }


    array_push($result,$tmp);
}
echo json_encode($result);
function getSingleEvent($id){

    $orderpath="../Orders/".$id."/";
    $eventfile=$orderpath.$id.".txt";

    if(!file_exists($eventfile)){
       // echo "no file found at " . $eventfile."\n";
       return;
    }

    $file=fopen($eventfile,"r") or die(-1);
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


    while (!feof($file)){
        $tmp=fgets($file);
        //echo $tmp;
        $tmp=substr($tmp,0,-1);
        if($tmp==$userId){
            $arr['isMember']=true;
        }
        if($tmp!=""){
            $user++;
        }

    }
    $arr["personCount"]=$user;
    return $arr;
}
